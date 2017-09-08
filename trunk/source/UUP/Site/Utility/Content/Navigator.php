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

namespace UUP\Site\Utility\Content;

/**
 * The page path navigator.
 * 
 * This class helps generator page navigation. Given an path, it aids in output the
 * navigation menu for its parent components. The default output formating gives an
 * simple parent path navigator.
 * 
 * @property string $format The output format.
 * @property string $path The page path.
 * @property string $base The base URL.
 * @property-read array $data The prepared menu items.
 * 
 * @author Anders Lövgren (QNET/BMC CompDept)
 */
class Navigator
{

        /**
         * The page path.
         * @var string 
         */
        private $_path;
        /**
         * The base URL.
         * @var string 
         */
        private $_base;
        /**
         * The link formatter.
         * @var string 
         */
        private $_format = "<a href=\"@base@/@path@\">@name@</a>";
        /**
         * The prepared menu items.
         * @var array 
         */
        private $_data;

        /**
         * Constructor.
         * @param string $path The page path.
         */
        public function __construct($path, $base)
        {
                $this->_path = $path;
                $this->_base = $base;
        }

        public function __get($name)
        {
                switch ($name) {
                        case 'format':
                                return $this->_format;
                        case 'path':
                                return $this->_path;
                        case 'base':
                                return $this->_base;
                        case 'data':
                                return $this->_data;
                }
        }

        public function __set($name, $value)
        {
                switch ($name) {
                        case 'format':
                                $this->_format = (string) $value;
                                break;
                        case 'path':
                                $this->_path = (string) $value;
                                break;
                        case 'base':
                                $this->_base = (string) $value;
                                break;
                }
        }

        /**
         * Prepare menu items.
         */
        public function prepare()
        {
                $parts = explode('/', $this->_path);
                $subst = array();
                $hrefs = array();

                for ($i = 0; $i < count($parts); ++$i) {
                        $subst[] = array(
                                '@base@' => $this->_base,
                                '@path@' => implode('/', array_slice($parts, 0, $i + 1)),
                                '@name@' => $parts[$i]
                        );
                }

                for ($i = 0; $i < count($subst); ++$i) {
                        $hrefs[] = str_replace(array_keys($subst[$i]), $subst[$i], $this->_format);
                }

                $this->_data = $hrefs;
        }

        /**
         * Render navigator menu.
         */
        public function render()
        {
                $this->prepare();
                $this->output();
        }

        private function output()
        {
                echo implode(' / ', $this->_data);
        }

}
