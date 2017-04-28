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

use InvalidArgumentException;

/**
 * Request and page dispatch parameters.
 * 
 * @property-read string $path The file path (relative).
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
         * The path relative to project.
         * @var string 
         */
        private $_path;
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
         * @param string $docs The public document path.
         */
        public function __construct($docs)
        {
                $this->_page = filter_input(INPUT_SERVER, 'SCRIPT_NAME');
                $this->_file = filter_input(INPUT_SERVER, 'SCRIPT_FILENAME');
                $this->_path = dirname(substr($this->_file, strlen($docs) + 1));
        }

        public function __get($name)
        {
                switch ($name) {
                        case 'path':
                                return $this->_path;
                        case 'page':
                                return $this->_page;
                        case 'file':
                                return $this->_file;
                        case 'data':
                                return array(
                                        'path' => $this->_path,
                                        'page' => $this->_page,
                                        'file' => $this->_file
                                );
                        default:
                                return $this->getParam($name);
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
         * Set request parameter filter.
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
         * Add request parameter filter.
         * 
         * @param string $name The input parameter name.
         * @param string $regex The regex pattern.
         */
        public function addFilter($name, $regex)
        {
                $this->_filter[$name] = $regex;
        }

        /**
         * Remove request parameter filter. 
         * @param string $name The input parameter name.
         */
        public function removeFilter($name)
        {
                unset($this->_filter[$name]);
        }

        /**
         * Check if request parameter exist.
         * 
         * @param string $name The input parameter name.
         * @param mixed $default The default value.
         * @param int $method The request method (GET and POST).
         * @return boolean 
         */
        public function hasParam($name, $method = INPUT_GET | INPUT_POST)
        {
                if ($method & INPUT_GET) {
                        if (filter_has_var(INPUT_GET, $name)) {
                                return true;
                        }
                }
                if ($method & INPUT_POST) {
                        if (filter_has_var(INPUT_POST, $name)) {
                                return true;
                        }
                }

                return false;
        }

        /**
         * Get request parameter value.
         * 
         * @param string $name The input parameter name.
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
                        throw new InvalidArgumentException(_("Request parameter $name don't match input filter"));
                } else {
                        return $value;
                }
        }

        /**
         * Get all request parameters.
         * 
         * If $names is null (default), then all request parameters are returned. The
         * $method is a bitmask of INPUT_GET and/or INPUT_POST. The defaults for missing
         * request parameters can be passed in the names argument:
         * 
         * <code>
         * $names = array(
         *      'name' => 'Anders',
         *      'work' => false
         * );
         * $request->getParams($names);
         * </code>
         * 
         * @param array $names The input parameter names.
         * @param int $method The request method (GET and POST).
         */
        public function getParams($names = null, $method = INPUT_GET | INPUT_POST)
        {
                $result = array();

                if (!isset($names)) {
                        $names = array();

                        if ($method & INPUT_GET) {
                                $names = array_merge($names, array_keys($_GET));
                        }
                        if ($method & INPUT_GET) {
                                $names = array_merge($names, array_keys($_POST));
                        }

                        $names = array_fill_keys($names, false);
                }

                foreach ($names as $key => $val) {
                        $result[$key] = $this->getParam($key, $val, $method);
                }

                return $result;
        }

        /**
         * Get request parameter value.
         * 
         * @param string $name The input parameter name.
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
