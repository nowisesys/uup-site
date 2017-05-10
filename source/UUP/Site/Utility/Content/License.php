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
 * License config class.
 * 
 * @property string $path The license directory path.
 * @property-read string $file The license file.
 * @property-read string $name The license name.
 * @property-read string $project The project name.
 * @property-read string $company The company name.
 * @property-read string $location The absolute file path.
 * 
 * @author Anders Lövgren (QNET/BMC CompDept)
 * @package UUP
 * @subpackage Site
 */
class License
{

        /**
         * The license options.
         * @var array 
         */
        private $_license;

        /**
         * Constructor.
         * @param array $license The license config.
         */
        public function __construct($license)
        {
                $this->_license = $license;
        }

        public function __get($name)
        {
                if (isset($this->_license[$name])) {
                        return $this->_license[$name];
                } elseif ($name == 'location') {
                        return sprintf("%s/%s", $this->path, $this->file);
                } elseif ($name == 'file') {
                        return 'apache-2.0.txt';
                } else {
                        return '';
                }
        }

        public function __set($name, $value)
        {
                if ($name == 'path') {
                        $this->_license['path'] = (string) $value;
                }
        }

}
