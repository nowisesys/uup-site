<?php

/*
 * Copyright (C) 2017 Anders Lövgren (Nowise Systems/BMC-IT, Uppsala University).
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace UUP\Site\Request;

use DomainException;
use RuntimeException;
use UUP\Site\Page\Context\Headers;
use UUP\Site\Utility\Config;
use UUP\Site\Utility\Locale;
use UUP\Site\Utility\Profile;
use UUP\Site\Utility\Security\Authentication;
use UUP\Site\Utility\Security\Session;

/**
 * The request handler.
 * 
 * @property-read Session $session The session object.
 * @property-read Authentication $auth The stack of authenticators.
 * @property-read Headers $headers Custom HTTP headers.
 * 
 * @property Error $error The error handler.
 * 
 * @author Anders Lövgren (Nowise Systems/BMC-IT, Uppsala University)
 * @package UUP
 * @subpackage Site
 */
abstract class Handler
{

        /**
         * The site configuration object.
         * @var Config 
         */
        public $config;
        /**
         * The locale settings object.
         * @var Locale 
         */
        public $locale;
        /**
         * Performance profiler.
         * @var Profile 
         */
        public $profile;
        /**
         * The request parameters.
         * @var Params 
         */
        public $params;

        /**
         * Constructor.
         * @param string $config The defaults.site configuration file.
         */
        public function __construct($config = null)
        {
                set_exception_handler(array($this, 'onException'));

                $this->profile = new Profile('lifetime::handler');
                $this->profile->start();

                $this->profile->push('handler::init');
                $this->profile->start();
                
                $this->profile->push('load::config');
                $this->profile->start();
                $this->config = new Config($config);
                $this->profile->stop();
                
                $this->profile->push('load::locale');
                $this->profile->start();
                $this->locale = new Locale($this->config);
                $this->profile->stop();
                
                $this->profile->push('load::params');
                $this->profile->start();
                $this->params = new Params($this->config->docs);
                $this->profile->stop();

                $this->profile->push('session::start');
                $this->profile->start();
                if ($this->config->session) {
                        if (!$this->session->started()) {
                                $this->session->start();
                        }
                }
                $this->profile->stop(); // session
                $this->profile->stop(); // handler
        }

        public function __destruct()
        {
                $this->profile->stop();

                if ($this->config->debug) {
                        error_log(print_r(array(
                                'config'  => $this->config->data,
                                'session' => $this->session->data,
                                'profile' => $this->profile->data,
                                'params'  => $this->params->data
                                ), true)
                        );
                }
        }

        public function __get($name)
        {
                switch ($name) {
                        case 'session':
                                $this->session = $this->getSession();
                                return $this->session;
                        case 'auth':
                                $this->auth = $this->getAuthentication();
                                return $this->auth;
                        case 'headers':
                                $this->headers = $this->getHeaders();
                                return $this->headers;
                        case 'error':
                                $this->error = $this->getErrorhandler();
                                return $this->error;
                }
        }

        public function __set($name, $value)
        {
                switch ($name) {
                        case 'session':
                                $this->session = $value;
                                break;
                        case 'auth':
                                $this->auth = $value;
                                break;
                        case 'headers':
                                $this->headers = $value;
                                break;
                        case 'error':
                                $this->error = $value;
                                break;
                }
        }

        /**
         * Get custom HTTP headers.
         * @return Headers
         */
        private function getHeaders()
        {
                return new Headers($this->config->headers);
        }

        /**
         * Get session object.
         * @return Session
         */
        private function getSession()
        {
                return new Session($this->config->session);
        }

        /**
         * Get page authentication.
         * @return Authentication
         */
        private function getAuthentication()
        {
                return new Authentication($this->config->auth['config']);
        }

        /**
         * Get error handler object.
         * @return Error
         */
        private function getErrorhandler()
        {
                return new Error($this->config->exception['handler']);
        }

        /**
         * Output extra HTML headers.
         * 
         * Override this method to output custom headers not provided by the UI theme.
         * This functionality is typical used for meta info related to pages in a 
         * sub directory.
         */
        public function printHeader()
        {
                if ($this->config->headers) {
                        echo "\n";
                        foreach ($this->headers as $tag => $attributes) {
                                foreach ($attributes as $attr) {
                                        echo "\t\t<$tag";
                                        foreach ($attr as $key => $val) {
                                                echo " $key=\"$val\"";
                                        }
                                        echo " />\n";
                                }
                        }
                }
                if ($this->config->content) {
                        echo "\n";
                        foreach ($this->content as $key => $val) {
                                if ($key == 'name') {
                                        printf("<meta name=\"application-name\" content=\"%s\">\n", $val);
                                } elseif ($key == 'info') {
                                        printf("<meta name=\"description\" content=\"%s\">\n", $val);
                                } elseif ($key == 'tags') {
                                        printf("<meta name=\"keywords\" content=\"%s\">\n", implode(',', $val));
                                }
                        }
                }
        }

        /**
         * Detect AJAX request.
         * 
         * Returns true if the server magic array contains HTTP_X_REQUESTED_WITH
         * with value XMLHttpRequest.
         * @return boolean
         */
        public function isAjax()
        {
                if (filter_has_var(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH') == false) {
                        return false;
                }
                if (filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH') != 'XMLHttpRequest') {
                        return false;
                }

                return true;
        }

        /**
         * Content is editable.
         * @return boolean
         */
        public function isEditable()
        {
                return false;
        }

        /**
         * Put page content.
         * 
         * Trap method for updating the page content targetted by this request. Needs to
         * be implemented by sub class. Call setContent() to handle the actual content 
         * writing.
         * 
         * @param string $content The page content.
         */
        protected function putContent($content)
        {
                try {
                        throw new RuntimeException("Setting page content is unsupported");
                } catch (RuntimeException $exception) {
                        echo json_encode(array(
                                'status'  => 'failure',
                                'message' => $exception->getMessage(),
                                'code'    => $exception->getCode()
                        ));
                } finally {
                        exit(0);
                }
        }

        /**
         * Write page content.
         * 
         * This method write content to file while checking required permissions of method 
         * caller.
         * 
         * @param string $content The page content.
         * @param string $target The target file.
         * @return int The number of bytes written.
         * @throws RuntimeException
         */
        protected function setContent($content, $target)
        {
                if (!$this->session->authenticated()) {
                        throw new RuntimeException("Caller is not authenticated");
                }
                if (!in_array($this->session->user, $this->config->edit['user'])) {
                        throw new RuntimeException("Caller is not a page editor");
                }

                if (!is_writable($target)) {
                        throw new RuntimeException("The target view is not writable");
                }

                if (($bytes = file_put_contents($target, trim($content))) == false) {
                        throw new RuntimeException("Failed write view content");
                }

                return $bytes;
        }

        /**
         * Validate request is secured (authenticated).
         * @return boolean
         * @throws DomainException
         */
        protected function validate()
        {
                try {
                        $this->profile->push('validate');
                        $this->profile->start();

                        if (!$this->config->session) {
                                throw new DomainException(_("Session handling is not enabled"));
                        }
                        if ($this->session->started() == false) {
                                $this->session->start();
                        }
                        if ($this->session->authenticated()) {
                                $this->session->verify();
                                return true;
                        }
                        if ($this->session->expiring()) {
                                $this->session->refresh();
                                return true;
                        }
                        if ($this->session->expired()) {
                                $this->session->destroy();
                        }
                        if (!$this->config->auth['sso']) {
                                $this->session->return = filter_input(INPUT_SERVER, 'REQUEST_URI');
                                return false;
                        }
                        if ($this->auth->accepted()) {
                                $this->session->create(
                                    $this->auth->name, $this->auth->user
                                );
                                return true;
                        } else {
                                $this->session->return = filter_input(INPUT_SERVER, 'REQUEST_URI');
                                return false;
                        }
                } finally {
                        $this->profile->stop();
                }
        }

        /**
         * The exception handler.
         * @param \Exception $exception The exception to report.
         */
        public function onException($exception)
        {
                $this->error->addHandler('*', true);
                $this->error->handle($this->config->uri, $exception);
        }

        /**
         * Render page content.
         */
        abstract public function render();
}
