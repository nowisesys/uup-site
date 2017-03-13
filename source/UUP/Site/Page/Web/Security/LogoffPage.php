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
         * View fragment pages.
         * @var array 
         */
        private $_pages = array(
                'destroy'   => 'destroy.phtml',
                'authent'   => 'authent.phtml',
                'completed' => 'completed.phtml'
        );

        /**
         * Constructor.
         * @param array $pages The logon pages.
         */
        public function __construct($pages = null)
        {
                parent::__construct(_("Logoff"));

                $name = filter_input(INPUT_GET, 'auth');
                $ajax = filter_input(INPUT_GET, 'ajax', FILTER_VALIDATE_BOOLEAN);

                if ($ajax) {
                        $this->setTemplate(null);       // Don't render in template
                        $this->_ajax = true;
                } else {
                        $this->_ajax = false;
                }

                $this->setPages($pages);
                $this->setState($name);
                $this->setMethod($name);

                $this->process();
        }

        public function printContent()
        {
                if (!$this->_ajax) {
                        SecurePage::printTitle(_("Logoff Page"));
                }

                $auth = $this->_auth;
                $ajax = $this->_ajax;
                $desc = $this->_desc;
                $name = $this->_name;

                if ($this->_step == self::STEP_SESSION_DESTROY) {
                        include($this->_pages['destroy']);
                } elseif ($this->_step == self::STEP_AUTHENT_LOGOUT) {
                        include($this->_pages['authent']);
                } elseif ($this->_step == self::STEP_LOGOUT_COMPLETED) {
                        include($this->_pages['completed']);
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
         * Set logout method.
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
         * Set current logout step.
         * @param string $name The authenticator name.
         */
        private function setState($name)
        {
                $this->_step = $this->getState($name);
        }

        /**
         * Get current logout step.
         * @param string $name The authenticator name.
         * @return int
         */
        private function getState($name)
        {
                if ($this->session->authenticated()) {
                        return self::STEP_SESSION_DESTROY;
                } elseif ($name) {
                        return self::STEP_AUTHENT_LOGOUT;
                } else {
                        return self::STEP_LOGOUT_COMPLETED;
                }
        }

        /**
         * Trigger logout.
         */
        private function logout()
        {
                $this->auth->activate($this->_name);
                $this->auth->logout();
        }

        /**
         * Destroy current session.
         */
        private function destroy()
        {
                $this->_name = $this->session->auth;
                $this->session->destroy();
        }

        /**
         * Process current request.
         */
        private function process()
        {
                if ($this->_step == self::STEP_AUTHENT_LOGOUT) {
                        $this->logout();
                }
                if ($this->_step == self::STEP_SESSION_DESTROY) {
                        $this->destroy();
                }
                if ($this->_name) {
                        $this->auth->activate($this->_name);
                }
                if (($auth = $this->auth->getAuthenticator())) {
                        $this->_auth = $auth;
                        $this->_desc = $auth->description;
                }
        }

}
