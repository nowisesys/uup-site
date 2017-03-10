<?php

/*
 * Copyright (C) 2017 Anders Lövgren (QNET/BMC CompDept).
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
 * @property-read Profile $profile The performance profiler.
 * 
 * @author Anders Lövgren (QNET/BMC CompDept)
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
         * Constructor.
         * @param string $config The defaults.site configuration file.
         */
        public function __construct($config = null)
        {
                $this->profile = new Profile();
                $this->profile->start();

                $this->config = new Config($config);
                $this->locale = new Locale($this->config);

                if ($this->config->session) {
                        if (!$this->session->started()) {
                                $this->session->start();
                        }
                }
        }

        public function __destruct()
        {
                $this->profile->stop();

                if ($this->config->debug) {
                        error_log(print_r(array(
                                'config'  => $this->config->data,
                                'session' => $this->session->data,
                                'profile' => $this->profile->data
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
         * Validate request is secured (authenticated).
         * @return boolean
         */
        protected function validate()
        {                
                try {
                        $this->profile->push('validate');
                        $this->profile->start();

                        if (!$this->config->session) {
                                throw new \Exception(_("Session handling is not enabled"));
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
         * Render page content.
         */
        abstract public function render();
}
