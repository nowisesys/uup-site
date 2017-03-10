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

namespace UUP\Site\Page\Web\Security;

use UUP\Authentication\Authenticator\Authenticator;
use UUP\Site\Page\Web\StandardPage;

/**
 * The logon page.
 *
 * @author Anders Lövgren (QNET/BMC CompDept)
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
         * Called as AJAX request?
         * @var boolean 
         */
        protected $_ajax;
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
         * @var string|boolean 
         */
        protected $_form = false;
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
         * Constructor.
         * @param array $pages The logon pages.
         */
        public function __construct($pages = null)
        {
                parent::__construct(_("Logon"));

                $form = filter_input(INPUT_GET, 'form');
                $name = filter_input(INPUT_GET, 'auth');
                $ajax = filter_input(INPUT_GET, 'ajax', FILTER_VALIDATE_BOOLEAN);

                if ($ajax) {
                        $this->_ajax = true;
                        $this->setTemplate(null);       // Don't render in template
                } else {
                        $this->_ajax = false;
                }
                if ($form) {
                        $this->_form = $form;
                }

                $this->setPages($pages);
                $this->setState($name);
                $this->setMethod($name);

                $this->process();
        }

        public function printContent()
        {
                if (!$this->_ajax) {
                        SecurePage::printTitle(_("Logon Page"));
                }

                // 
                // Inject commonly used variables:
                // 
                $auth = $this->_auth;
                $ajax = $this->_ajax;
                $desc = $this->_desc;
                $name = $this->_name;
                $form = $this->_form;

                if ($this->_step == self::STEP_ALREADY_LOGGED_ON) {
                        include($this->_pages['secure']);
                } elseif ($this->_step == self::STEP_SELECT_METHOD) {
                        if ($this->_ajax) {
                                include($this->_pages['select']);
                        } elseif ($this->_form) {
                                include($this->_pages['form']);
                        } else {
                                include($this->_pages['normal']);
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

}
