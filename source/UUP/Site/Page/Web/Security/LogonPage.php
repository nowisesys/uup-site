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
         *
         * @var array 
         */
        private $_pages;

        /**
         * Constructor.
         * @param array $pages The logon pages.
         */
        public function __construct($pages = null)
        {
                parent::__construct(_("Logon"));

                $this->_name = filter_input(INPUT_GET, 'auth');
                $this->_ajax = filter_input(INPUT_GET, 'ajax', FILTER_VALIDATE_BOOLEAN);

                if ($this->_ajax) {
                        $this->setTemplate(null);       // Don't render in template
                }

                if (!isset($pages)) {
                        $this->_pages = array('select' => 'select.phtml', 'normal' => 'normal.phtml');
                } else {
                        $this->_pages = $pages;
                }

                if ($this->session->authenticated()) {
                        $this->_step = self::STEP_ALREADY_LOGGED_ON;
                        $this->_name = $this->session->auth;
                } elseif ($this->_name) {
                        $this->_step = self::STEP_METHOD_SELECTED;

                        $this->auth->activate($this->_name);
                        $this->auth->login();

                        if ($this->session->return) {
                                $this->redirect($this->session->return);
                                $this->session->return = false;
                        } elseif ($this->config->auth['start']) {
                                $this->redirect($this->config->auth['start']);
                        }
                } else {
                        $this->_step = self::STEP_SELECT_METHOD;
                }

                if ($this->_name) {
                        $this->auth->activate($this->_name);

                        $this->_auth = $this->auth->getAuthenticator();
                        $this->_desc = $this->auth->getAuthenticator()->description;
                }
        }

        public function printContent()
        {
                if (!$this->_ajax) {
                        printf("<h1>%s</h1>\n", _("Logon Page"));
                }

                if ($this->_step == self::STEP_ALREADY_LOGGED_ON) {
                        printf("<p><span><i class=\"fa fa-check\" style=\"color: #33cc33\"></i></span> %s</p>\n", sprintf(_("Logged on using %s"), $this->_desc));
                        printf("<span style=\"margin: 20px\"><input type=\"button\" class=\"w3-btn w3-blue\" onclick=\"window.location='%s'\" value=\"%s\"></span>\n", $this->config->url($this->config->auth['logoff']), _("Logoff"));
                } elseif ($this->_step == self::STEP_METHOD_SELECTED) {
                        
                } elseif ($this->_step == self::STEP_SELECT_METHOD) {
                        if ($this->_ajax) {
                                include($this->_pages['select']);
                        } else {
                                printf("<p>%s</p>\n", _("Choose logon method by clicking one of the buttons:"));
                                include($this->_pages['normal']);
                        }
                }
        }

}
