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
class LogoffPage extends StandardPage
{

        /**
         * Destroy session step.
         */
        const STEP_SESSION_DESTROY = 1;
        /**
         * Logout from authenticator.
         */
        const STEP_AUTHENT_LOGOUT = 2;
        /**
         * The logout is all completed.
         */
        const STEP_LOGOUT_COMPLETED = 3;

        /**
         * The authenticator name.
         * @var string 
         */
        protected $_name;
        /**
         * Called as AJAX request?
         * @var boolean 
         */
        protected $_ajax;
        /**
         * The currently used authenticator.
         * @var Authenticator
         */
        protected $_auth;
        /**
         * The authenticator description.
         * @var string 
         */
        protected $_desc;
        /**
         * The current logout step.
         * @var int 
         */
        protected $_step;

        /**
         * Constructor.
         */
        public function __construct()
        {
                parent::__construct(_("Logoff"));

                $this->_name = filter_input(INPUT_GET, 'auth');
                $this->_ajax = filter_input(INPUT_GET, 'ajax', FILTER_VALIDATE_BOOLEAN);

                if ($this->_ajax) {
                        $this->setTemplate(null);       // Don't render in template
                }

                if ($this->session->authenticated()) {
                        $this->_step = self::STEP_SESSION_DESTROY;

                        $this->_name = $this->session->auth;
                        $this->session->destroy();
                } elseif ($this->_name) {
                        $this->_step = self::STEP_AUTHENT_LOGOUT;

                        $this->auth->activate($this->_name);
                        $this->auth->logout();
                } else {
                        $this->_step = self::STEP_LOGOUT_COMPLETED;
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
                        printf("<h1>%s</h1>\n", _("Logoff Page"));
                }

                if ($this->_step == self::STEP_SESSION_DESTROY) {
                        if (!$this->_ajax) {
                                printf("<p><span><i class=\"fa fa-check\" style=\"color: #33cc33; min-width: 20px\"></i></span> %s</p>\n", ("Your logon session has been destroyed."));
                        }
                        if ($this->_auth) {
                                printf("<p><span><i class=\"fa fa-info\" style=\"color: #3366ff; min-width: 20px\"></i></span> %s</p>\n", sprintf(_("You are still logged on to %s:"), $this->_desc));
                                printf("<span style=\"margin: 20px\"><input type=\"button\" class=\"w3-btn w3-blue\" onclick=\"window.location='?auth=%s'\" value=\"%s\"></span>\n", $this->_name, sprintf(_("Logout from %s"), $this->_auth->name));
                        }
                } elseif ($this->_step == self::STEP_AUTHENT_LOGOUT) {
                        printf("<p><span><i class=\"fa fa-check\" style=\"color: #33cc33; min-width: 20px\"></i></span> %s</p>\n", sprintf(_("You have been logged out from %s."), $this->_desc));
                } elseif ($this->_step == self::STEP_LOGOUT_COMPLETED) {
                        error_log($this->_ajax);
                        printf("<p><span><i class=\"fa fa-check\" style=\"color: #33cc33; min-width: 20px\"></i></span> %s</p>\n", _("You are currently not logged on."));
                        printf("<span style=\"margin: 20px\"><input type=\"button\" class=\"w3-btn w3-blue\" onclick=\"window.location='%s'\" value=\"%s\"></span>\n", $this->config->url($this->config->auth['logon']), _("Logon"));
                }
        }

}
