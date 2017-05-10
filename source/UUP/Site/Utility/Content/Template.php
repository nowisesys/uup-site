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
 * Template file class.
 * 
 * @property string $source The source template.
 * @property string $target The target file.
 * @property string $author The page author.
 * @property string $suffix The class suffix.
 * @property string $name The page name.
 * @property License $license The license file.
 * 
 * @author Anders Lövgren (QNET/BMC CompDept)
 * @package UUP
 * @subpackage Site
 */
class Template
{

        /**
         * The template file.
         * @var string 
         */
        private $_source;
        /**
         * The target file.
         * @var string 
         */
        private $_target;
        /**
         * The page author.
         * @var string 
         */
        private $_author;
        /**
         * The class suffix.
         * @var string 
         */
        private $_suffix = 'Page';
        /**
         * The page name.
         * @var string 
         */
        private $_name;
        /**
         * The license object.
         * @var License 
         */
        private $_license;

        /**
         * Constructor.
         * @param array|License $license The license information.
         */
        public function __construct($license)
        {
                if (is_object($license)) {
                        $this->_license = $license;
                } else {
                        $this->_license = new License($license);
                }
        }

        public function __get($name)
        {
                switch ($name) {
                        case 'source':
                                return $this->_source;
                        case 'target':
                                return $this->_target;
                        case 'author':
                                return $this->_author;
                        case 'suffix':
                                return $this->_suffix;
                        case 'name':
                                return $this->_name;
                        case 'license':
                                return $this->_license;
                }
        }

        public function __set($name, $value)
        {
                switch ($name) {
                        case 'source':
                                $this->_source = (string) $value;
                                break;
                        case 'target':
                                $this->_target = (string) $value;
                                break;
                        case 'author':
                                $this->_author = (string) $value;
                                break;
                        case 'suffix':
                                $this->_suffix = (string) $value;
                                break;
                        case 'name':
                                $this->_name = (string) $value;
                                break;
                        case 'license':
                                $this->_license = $value;
                                break;
                }
        }

        /**
         * Output new file based on template.
         * 
         * @param string $name The class name.
         * @throws \RuntimeException
         */
        public function output($name = null)
        {
                if (!file_exists($this->_source)) {
                        throw new \RuntimeException("The source file don't exist");
                }
                if (file_exists($this->_target)) {
                        throw new \RuntimeException("The target file already exist");
                }
                if (!isset($name)) {
                        $name = $this->_name;
                }

                $subst = array(
                        '@file@'     => basename($this->_target),
                        '@date@'     => date('Y-m-d'),
                        '@time@'     => date('H:i:s'),
                        '@year@'     => date('Y'),
                        '@author@'   => $this->_author,
                        '@datetime@' => strftime("%x %X"),
                        '@name@'     => sprintf("%s%s", ucfirst($name), $this->_suffix),
                        '@title@'    => ucfirst($name),
                        '@project@'  => $this->_license->project,
                        '@company@'  => $this->_license->company
                );

                error_log(print_r($this->_license, true));

                $license = file_get_contents($this->_license->location);

                $content = file_get_contents($this->_source);
                $content = str_replace('@license@', $license, $content);
                $content = str_replace(array_keys($subst), $subst, $content);

                if (!file_put_contents($this->_target, $content)) {
                        throw new \RuntimeException("Failed create target file");
                }
        }

        /**
         * Get camelized name.
         * @param string $target The target file.
         */
        public function camelize($target)
        {
                if (empty($target)) {
                        return null;
                }

                if (($pos = strpos($target, '/')) !== false) {
                        $target = basename($target);
                }
                if (($pos = strpos($target, '.')) !== false) {
                        $target = substr($target, 0, $pos);
                }
                if (($pos = strpos($target, '-')) === false) {
                        $target = ucfirst($target);
                } else {
                        $pieces = explode('-', $target);
                        $pieces = array_map('ucfirst', $pieces);
                        $target = implode("", $pieces);
                }

                return $target;
        }

}
