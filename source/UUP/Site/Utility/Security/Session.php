<?php

/*
 * Copyright (C) 2016-2017 Anders Lövgren (Computing Department at BMC, Uppsala University).
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

use DomainException;
use LogicException;
use RuntimeException;

/**
 * Provides session management and verification of user authentication.
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
 * @property string $return The return URL.
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
         * The session entry name.
         */
        const SESSKEY = 'logon';

        /**
         * The session name.
         * @var string 
         */
        private $_name;
        /**
         * The session data.
         * @var array 
         */
        private $_data = array('expires' => 0);
        /**
         * The session data is dirty and need to be saved.
         * @var boolean 
         */
        private $_dirty = false;

        /**
         * Constructor.
         * @param string|boolean $start The session name.
         * @throws \LogicException
         */
        public function __construct($start = false)
        {
                if ($start) {
                        $this->setup($start);
                }
        }

        public function __destruct()
        {
                if ($this->_dirty) {
                        $this->write();
                }
        }

        public function __get($name)
        {
                switch ($name) {
                        case 'return':
                                return $this->get('return');
                        case 'auth':
                                return $this->get('auth');
                        case 'user':
                                return $this->get('user');
                        case 'peer':
                                return $this->get('peer');
                        case 'expires':
                                return $this->get('expires');
                        case 'refresh':
                                return $this->get('refresh');
                        case 'data':
                                return $this->_data;
                }
        }

        public function __set($name, $value)
        {
                if ($name == 'return') {
                        $this->set($name, $value);
                }
        }

        /**
         * Check if session is authenticated.
         * @return boolean
         */
        public function authenticated()
        {
                return isset($this->_data['user']);
        }

        /**
         * Verify session data.
         * @throws \DomainException|RuntimeException
         */
        public function verify()
        {
                if (!$this->auth) {
                        throw new DomainException(_("Logon method is unset"));
                }
                if (!$this->user) {
                        throw new RuntimeException(_("User is not authenticated"));
                }
                if ($this->peer != filter_input(INPUT_SERVER, 'REMOTE_ADDR')) {
                        throw new RuntimeException(_("Remote address missmatch"));
                }
                if ($this->refresh < time()) {
                        $this->refresh();
                }
                if ($this->expires < time()) {
                        throw new RuntimeException(_("Session has expired"));
                }
        }

        /**
         * Is session about to expire?
         * @return boolean
         */
        public function expiring()
        {
                return $this->refresh < time() && $this->expires > time();
        }

        /**
         * Has session expired?
         * @return boolean
         */
        public function expired()
        {
                return $this->expires != 0 && $this->expires < time();
        }

        /**
         * Refresh expire and renewal time.
         */
        public function refresh()
        {
                $this->set('refresh', time() + self::REFRESH);
                $this->set('expires', time() + self::EXPIRES);

                session_regenerate_id();
        }

        /**
         * Create session.
         * @param string $auth The authenticator name.
         * @param string $user The logged on user.
         */
        public function create($auth, $user)
        {
                $this->set('auth', $auth);
                $this->set('user', $user);
                $this->set('peer', filter_input(INPUT_SERVER, 'REMOTE_ADDR'));
                $this->set('refresh', time() + self::REFRESH);
                $this->set('expires', time() + self::EXPIRES);

                session_regenerate_id();
        }

        /**
         * Destroy and regenerate session ID.
         */
        public function destroy()
        {
                session_regenerate_id(true);
                session_destroy();
        }

        /**
         * Set sessions data.
         * @param string $key The data key.
         * @param string $val The data value.
         */
        private function set($key, $val)
        {
                $this->_data[$key] = $val;
                $this->_dirty = true;
        }

        /**
         * Get session data.
         * 
         * @param string $key The data key.
         * @param string|int|bool $def Default value if unset.
         * @return string|int|bool
         */
        private function get($key = null, $def = false)
        {
                if (isset($this->_data[$key])) {
                        return $this->_data[$key];
                } else {
                        return $def;
                }
        }

        /**
         * Check if session has been started.
         * @return boolean
         */
        public function started()
        {
                return session_status() == PHP_SESSION_ACTIVE;
        }

        /**
         * Start session.
         * @throws RuntimeException
         */
        public function start()
        {
                if (is_string($this->_name)) {
                        session_name($this->_name);
                }
                if (!session_start()) {
                        throw new RuntimeException(_("Failed start session. Please check log files for the reason."));
                }
        }

        /**
         * Close any active session.
         */
        public function close()
        {
                if (session_status() == PHP_SESSION_ACTIVE) {
                        session_write_close();
                }
        }

        /**
         * Setup session.
         * @param string|boolean $start Either true or session name.
         * @throws LogicException
         */
        private function setup($start)
        {
                if (is_string($start)) {
                        $this->_name = $start;
                }

                if (session_status() == PHP_SESSION_DISABLED) {
                        throw new LogicException(_("Session are disabled. Please enable or disable authentication support."));
                }
                if (session_status() == PHP_SESSION_NONE) {
                        $this->start();
                }
                if (session_status() == PHP_SESSION_ACTIVE) {
                        $this->read();
                }
        }

        /**
         * Initialize session data from existing session.
         */
        private function read()
        {
                if (isset($_SESSION[self::SESSKEY])) {
                        $this->_data = $_SESSION[self::SESSKEY];
                }
        }

        /**
         * Write session data to persistent storage.
         */
        private function write()
        {
                $_SESSION[self::SESSKEY] = $this->_data;
                session_write_close();
                $this->_dirty = false;
        }

}
