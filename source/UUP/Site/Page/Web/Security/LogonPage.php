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

namespace UUP\Site\Page\Web\Security;

use UUP\Authentication\Authenticator\Authenticator;
use UUP\Authentication\Authenticator\RequestAuthenticator;
use UUP\Site\Page\Web\StandardPage;

/**
 * The logon page.
 *
 * @author Anders Lövgren (Nowise Systems/BMC-IT, Uppsala University)
 * @package UUP
 * @subpackage Site
 */
class LogonPage extends StandardPage
{

        /**
         * Select authentication method.
         */
        const STEP_SELECT_METHOD = 1;
        /**
         * Authentication method selected.
         */
        const STEP_METHOD_SELECTED = 2;
        /**
         * Caller is already authenticated.
         */
        const STEP_ALREADY_LOGGED_ON = 3;

        /**
         * The authenticator name.
         * @var string 
         */
        protected $_name;
        /**
         * The currently used authenticator.
         * @var Authenticator
         */
        protected $_auth;
        /**
         * The current step.
         * @var int 
         */
        protected $_step;
        /**
         * Description for active authenticator.
         * @var string 
         */
        protected $_desc;
        /**
         * Show authenticator form.
         * @var string|bool 
         */
        protected $_form = false;
        /**
         * The authenticator select type.
         * @var string 
         */
        protected $_type = 'normal';
        /**
         * Send JSON response.
         * @var bool 
         */
        protected $_json;
        /**
         * View fragment pages.
         * @var array 
         */
        private $_pages = array(
                'select' => 'select.phtml',
                'normal' => 'normal.phtml',
                'secure' => 'secure.phtml',
                'form'   => 'form.phtml'
        );
        /**
         * The logon is interactive (initiated by user).
         * @var bool 
         */
        private $_user = false;

        /**
         * Constructor.
         * @param array $pages The logon pages.
         */
        public function __construct($pages = null)
        {
                parent::__construct(_("Logon"));

                $form = filter_input(INPUT_GET, 'form', FILTER_SANITIZE_STRING);
                $name = filter_input(INPUT_GET, 'auth', FILTER_SANITIZE_STRING);
                $type = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_STRING);
                $json = filter_input(INPUT_GET, 'json', FILTER_VALIDATE_BOOLEAN);
                $user = filter_input(INPUT_GET, 'user', FILTER_VALIDATE_BOOLEAN);

                if ($json) {
                        $this->setTemplate(null);       // Don't render in template
                        $this->_json = true;
                }
                if ($form) {
                        $this->_form = $form;
                }
                if ($type) {
                        $this->_type = $type;
                }
                if ($user) {
                        $this->_user = $user;
                }

                $this->setPages($pages);
                $this->setState($name);
                $this->setMethod($name);

                $this->process();
        }

        public function printContent()
        {
                SecurePage::printTitle(_("Logon Page"));

                // 
                // Inject commonly used variables:
                // 
                $auth = $this->_auth;
                $desc = $this->_desc;
                $name = $this->_name;
                $form = $this->_form;

                if ($this->_step == self::STEP_ALREADY_LOGGED_ON) {
                        include($this->_pages['secure']);
                } elseif ($this->_step == self::STEP_SELECT_METHOD) {
                        if ($this->_user && $this->config->auth['start'] == false) {
                                $this->session->return = filter_input(INPUT_SERVER, 'HTTP_REFERER');
                        }
                        if ($this->_form) {
                                include($this->_pages['form']);
                        } else {
                                include($this->_pages[$this->_type]);
                        }
                }
        }

        /**
         * Set support page.
         * @param string $type The identifier (i.e. select).
         * @param string $page The page name
         */
        protected function setPage($type, $page)
        {
                $this->_pages[$type] = $page;
        }

        /**
         * Set support pages.
         * @param array $pages The support pages.
         */
        protected function setPages($pages)
        {
                if (isset($pages)) {
                        $this->_pages = $pages;
                }
        }

        /**
         * Set login method.
         * @param string $name The authenticator name.
         */
        private function setMethod($name)
        {
                if (is_null($name)) {
                        $this->_name = $this->session->auth;
                } else {
                        $this->_name = $name;
                }
        }

        /**
         * Set current login step.
         * @param string $name The authenticator name.
         */
        private function setState($name)
        {
                $this->_step = $this->getState($name);
        }

        /**
         * Get current login step.
         * @param string $name The authenticator name.
         * @return int
         */
        private function getState($name)
        {
                if ($this->session->authenticated()) {
                        return self::STEP_ALREADY_LOGGED_ON;
                } elseif ($name) {
                        return self::STEP_METHOD_SELECTED;
                } else {
                        return self::STEP_SELECT_METHOD;
                }
        }

        /**
         * Trigger login.
         * 
         * Calling this method will terminate the script and redirect browser to
         * current return URL saved in session or the configured start page.
         */
        private function login()
        {
                if (isset($this->_name)) {
                        $this->auth->activate($this->_name);
                        $this->auth->login();
                }

                $this->validate();

                if ($this->session->return) {
                        $this->redirect($this->session->return);
                        $this->session->return = false;
                } elseif ($this->config->auth['start']) {
                        $this->redirect($this->config->auth['start']);
                }
        }

        /**
         * Process current request.
         */
        private function process()
        {
                if ($this->_step == self::STEP_METHOD_SELECTED) {
                        $this->login();
                } elseif ($this->_json) {
                        $this->send();
                }
                if ($this->_name) {
                        $this->auth->activate($this->_name);
                }
                if ($this->_form) {
                        $this->auth->activate($this->_form);
                }
                if (($auth = $this->auth->getAuthenticator())) {
                        $this->_auth = $auth;
                        $this->_desc = $auth->description;
                }
        }

        /**
         * Send JSON encoded list of authenticators.
         */
        private function send()
        {
                $response = array(
                        'data' => $this->session->data,
                        'step' => $this->_step,
                        'auth' => array()
                );

                foreach ($this->auth->authenticators(true) as $name => $auth) {
                        if ($auth instanceof RequestAuthenticator) {
                                $response['auth'][$name] = array(
                                        'type'  => 'form',
                                        'name'  => $auth->name,
                                        'desc'  => $auth->description,
                                        'fname' => $auth->fname,
                                        'fuser' => $auth->fuser,
                                        'fpass' => $auth->fpass
                                );
                        } else {
                                $response['auth'][$name] = array(
                                        'type' => 'extern',
                                        'name' => $auth->name,
                                        'desc' => $auth->description,
                                );
                        }
                }

                header("Content-Type: application/json; charset=utf-8");
                echo json_encode($response);

                exit(0);
        }

}
