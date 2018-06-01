<?php

/*
 * Copyright (C) 2017 Anders Lövgren (Computing Department at BMC, Uppsala University).
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

namespace UUP\Site\Utility\Security;

use UUP\Authentication\Authenticator\Authenticator;
use UUP\Authentication\Stack\AuthenticatorRequiredException;
use UUP\Authentication\Stack\AuthenticatorStack;

/**
 * Authentication support.
 * 
 * @property-read string $user The logged on user.
 * @property-read string $name The accepted authenticator.
 *
 * @author Anders Lövgren (Nowise Systems/BMC-IT, Uppsala University)
 * @package UUP
 * @subpackage Site
 */
class Authentication extends AuthenticatorStack
{

        /**
         * The accepted authenticator.
         * @var string 
         */
        private $_name;
        /**
         * The user subject.
         * @var string 
         */
        private $_user;

        /**
         * Constructor.
         * 
         * The authenticator config is either passed as an path or array of data. 
         * If $config is unset, then AUTH_INC is used if defined.
         * 
         * @param string|array $config The authenticator config.
         */
        public function __construct($config = null)
        {
                if (is_array($config)) {
                        parent::__construct($config);
                } elseif (is_string($config)) {
                        parent::__construct(require($config));
                } elseif (defined('AUTH_INC')) {
                        parent::__construct(require(AUTH_INC));
                }

                if (count($this->_chain) == 0) {
                        error_log("No authenticators are defined");
                }
        }

        public function __get($name)
        {
                switch ($name) {
                        case 'user':
                                return $this->_user;
                        case 'name':
                                return $this->_name;
                }
        }

        /**
         * Check if any authenticator in the stack accepts the caller as a
         * logged in user. Throws exception if user is not authenticated using
         * a required authenticator.
         * 
         * @return bool
         * @throws AuthenticatorRequiredException
         */
        public function accepted()
        {
                // 
                // Return true if user is alredy set:
                // 
                if (isset($this->_user)) {
                        return true;
                }

                // 
                // All required authenticators must be accepted:
                // 
                foreach ($this->_chain['access'] as $name => $auth) {
                        if ($auth->control === Authenticator::REQUIRED) {
                                if (!$auth->accepted()) {
                                        throw new AuthenticatorRequiredException($auth);
                                }
                        }
                }

                // 
                // Make direct call if authenticator has been selected:
                // 
                if (isset($this->_name)) {
                        if (($auth = $this->getAuthenticator())) {
                                if ($auth->accepted()) {
                                        $this->_user = $auth->getSubject();
                                        return true;
                                }
                        }
                }

                // 
                // Only one sufficient authentication must be accepted:
                // 
                foreach ($this->_chain['auth'] as $name => $auth) {
                        if ($auth->control === Authenticator::SUFFICIENT) {
                                if ($auth->accepted()) {
                                        $this->_name = $name;
                                        $this->_user = $auth->getSubject();
                                        return true;
                                }
                        }
                }

                // 
                // No accepted authenticator:
                // 
                return false;
        }

        /**
         * Set active authenticator.
         * @param string $key The authenticator name.
         */
        public function activate($key)
        {
                $this->_name = $key;
        }

        /**
         * Logon using active authenticator.
         */
        public function login()
        {
                $this->getAuthenticator()->login();
        }

        /**
         * Logout using active authenticator.
         */
        public function logout()
        {
                $this->getAuthenticator()->logout();
        }

        /**
         * Get active authenticator.
         * @return Authenticator
         */
        public function getAuthenticator()
        {
                parent::activate($this->_name);
                return parent::getAuthenticator();
        }

}
