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

namespace UUP\Site\Utility;

/**
 * Fortune cookie class.
 * 
 * This class maintains and delivers fortune cookies using the fortune command. It can
 * also deliver an message of the days if passed to constructor.
 *
 * @author Anders Lövgren (Nowise Systems/BMC-IT, Uppsala University)
 * @package UUP
 * @subpackage Site
 */
class Fortune
{

        /**
         * The fortune cookie.
         * @var string 
         */
        private $_cookie;

        /**
         * Constructor.
         * @param string $cookie The fortune cookie.
         */
        public function __construct($cookie = null)
        {
                if ($cookie !== false) {
                        $this->setCookie($cookie);
                }
        }

        public function __get($name)
        {
                if ($name == 'cookie') {
                        return $this->_cookie;
                }
        }

        public function __set($name, $value)
        {
                if ($name == 'cookie') {
                        $this->_cookie = (string) $value;
                }
        }

        public function __isset($name)
        {
                if ($name == 'cookie') {
                        return isset($this->_cookie);
                }
        }

        public function __invoke()
        {
                echo $this->_cookie;
        }

        /**
         * Send cookie to stdout.
         */
        public function output()
        {
                echo $this->_cookie;
        }

        /**
         * Get fortune cookie.
         * @return string
         */
        public function getCookie()
        {
                return $this->_cookie;
        }

        /**
         * Set fortune cookie.
         * @param string $cookie The fortune cookie.
         */
        public function setCookie($cookie)
        {
                if (is_string($cookie)) {
                        $this->_cookie = $cookie;
                        return;
                }

                if (!extension_loaded('xcache')) {
                        $this->_cookie = shell_exec('fortune -s');
                        return;
                }

                $cachekey = $this->getCacheKey();

                if (xcache_isset($cachekey)) {
                        $this->_cookie = xcache_get($cachekey);
                } else {
                        xcache_unset_by_prefix("fortune");
                        $this->_cookie = shell_exec('fortune -s');
                        xcache_set($cachekey, $this->_cookie);
                }
        }

        /**
         * Get cache key.
         * @return string
         */
        private function getCacheKey()
        {
                return sprintf("fortune-cookie-%s", date('Y-m-d'));
        }

}
