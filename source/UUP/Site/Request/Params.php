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

namespace UUP\Site\Request;

/**
 * Request and page dispatch parameters.
 * 
 * @property-read string $page The requested page (location).
 * @property-read string $file The requested file (filename).
 * @property-read array $data The request data.
 * 
 * @author Anders Lövgren (QNET/BMC CompDept)
 * @package UUP
 * @subpackage Site
 */
class Params
{

        /**
         * The requested page.
         * @var string 
         */
        private $_page;
        /**
         * The requested file.
         * @var string 
         */
        private $_file;
        /**
         * Request parameter filter (regex).
         * @var array 
         */
        private $_filter = array();

        /**
         * Constructor.
         */
        public function __construct()
        {
                $this->_page = filter_input(INPUT_SERVER, 'SCRIPT_NAME');
                $this->_file = filter_input(INPUT_SERVER, 'SCRIPT_FILENAME');
        }

        public function __get($name)
        {
                switch ($name) {
                        case 'page':
                                return $this->_page;
                        case 'file':
                                return $this->_file;
                        case 'data':
                                return array(
                                        'page' => $this->_page,
                                        'file' => $this->_file
                                );
                        default:
                                return $this->param($name);
                }
        }

        /**
         * Set requested page (location).
         * 
         * @param string $page The requested page.
         */
        public function setPage($page)
        {
                $this->_page = $page;
        }

        /**
         * Set requested file (filename).
         * 
         * @param string $file The requested script.
         */
        public function setFile($file)
        {
                $this->_file = $file;
        }

        /**
         * Set input parameter filter.
         * 
         * <code>
         * // 
         * // Set regex filter for requets parameters:
         * // 
         * $filter = array(
         *      'date' => '/^\d{4,2}-\d{2}-\d{2}/$',
         *      'user' => '/^[a-z]+(@[\.a-z]+)?/$'
         * );
         * $params->setFilter($filter);
         * </code>
         * 
         * @param array $filter The array of regex.
         */
        public function setFilter($filter)
        {
                $this->_filter = $filter;
        }

        /**
         * Add input parameter filter.
         * 
         * @param string $name The parameter name.
         * @param string $regex The regex pattern.
         */
        public function addFilter($name, $regex)
        {
                $this->_filter[$name] = $regex;
        }

        /**
         * Get input parameter value.
         * 
         * @param string $name The parameter name.
         * @param mixed $default The default value.
         * @param int $method The request method (GET and POST).
         */
        public function getParam($name, $default = false, $method = INPUT_GET | INPUT_POST)
        {
                if (!($value = $this->getValue($name, $method))) {
                        return $default;
                }
                if (!array_key_exists($name, $this->_filter)) {
                        return $value;
                }
                if (!preg_match($this->_filter[$name], $value)) {
                        throw new \Exception("Request parameter $name don't match input filter");
                } else {
                        return $value;
                }
        }

        /**
         * Get input parameter value.
         * 
         * @param string $name The parameter name.
         * @param int $method The request method (GET and POST).
         * @return boolean
         */
        private function getValue($name, $method)
        {
                if ($method & INPUT_GET) {
                        if (($value = filter_input(INPUT_GET, $name))) {
                                return $value;
                        }
                }
                if ($method & INPUT_POST) {
                        if (($value = filter_input(INPUT_POST, $name))) {
                                return $value;
                        }
                }

                return false;
        }

}
