<?php

/*
 * Copyright (C) 2016 Anders Lövgren (Computing Department at BMC, Uppsala University).
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

namespace UUP\Site\Utility;

use UUP\Authentication\Stack\AuthenticatorStack;

/**
 * Provides session management and user authentication.
 * 
 * The session data for an authenticated session will contain:
 * 
 * <code>
 * array(
 *      'auth'    => string      // authentication method
 *      'user'    => string      // the username (subject)
 *      'peer'    => string      // remote host or ip-address
 *      'expires' => datetime    // expire timestamp
 *      'refresh' => datetime    // refresh timestamp
 * )
 * </code>
 * 
 * @property-read string $auth The authentication method.
 * @property-read string $user The authenticated user.
 * @property-read string $peer The remote hostname or ip-address.
 * @property-read int $expires The expires timestamp.
 * @property-read int $refresh The refresh timestamp.
 * 
 * @property-read array $data Get all session data.
 * 
 * @author Anders Lövgren (QNET/BMC CompDept)
 * @package UUP
 * @subpackage Site
 */
class Session
{

        /**
         * Valid for this number of seconds.
         */
        const EXPIRES = 14400;
        /**
         * Need refresh this number of seconds before expiring.
         */
        const REFRESH = 3600;

        /**
         * The authentication stack.
         * @var AuthenticatorStack 
         */
        private $_auth;
        /**
         * The session name.
         * @var string 
         */
        private $_name;
        /**
         * The session data.
         * @var array 
         */
        private $_data;
        /**
         * The session data is dirty and need to be saved.
         * @var boolean 
         */
        private $_dirty = false;

        /**
         * Constructor.
         * @param AuthenticatorStack $auth The authentication stack.
         * @param string $name The session name.
         * @throws \Exception
         */
        public function __construct($auth = null, $name = null)
        {
                $this->_auth = $auth;
                $this->_name = $name;

                if (session_status() == PHP_SESSION_DISABLED) {
                        throw new \Exception("Session are disabled. Please enable or disable authentication support.");
                }
                if (session_status() == PHP_SESSION_NONE) {
                        $this->start();
                }
                if (session_status() == PHP_SESSION_ACTIVE) {
                        $this->read();
                }
        }

        public function __destruct()
        {
                if ($this->_dirty) {
                        $this->start();
                        $this->save();
                }
        }

        public function __get($name)
        {
                switch ($name) {
                        case 'auth':
                                return $this->_data['auth'];
                        case 'user':
                                return $this->_data['user'];
                        case 'peer':
                                return $this->_data['peer'];
                        case 'expires':
                                return $this->_data['expires'];
                        case 'refresh':
                                return $this->_data['refresh'];
                        case 'data':
                                return $this->_data;
                }
        }

        public function authenticated()
        {
                return isset($this->_data['user']);
        }

        public function verify()
        {
                
        }

        public function refresh()
        {
                
        }

        private function create()
        {
                
        }

        private function write($key, $val)
        {
                $this->_data[$key] = $val;
                $this->_dirty = true;
        }

        private function start()
        {
                if (isset($this->_name)) {
                        session_name($this->_name);
                }
                if (!session_start()) {
                        throw new \Exception("Failed start session. Please check log files for the reason.");
                }
        }

        private function read()
        {
                $this->_data = $_SESSION['auth'];
                session_write_close();
        }

        private function save()
        {
                $_SESSION['auth'] = $this->_data;
                session_write_close();
                $this->_dirty = false;
        }

}
