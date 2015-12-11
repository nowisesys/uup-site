<?php

/*
 * Copyright (C) 2015 Anders Lövgren (QNET/BMC CompDept).
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
 * Site configuration class.
 *
 * @property string $root The top directory (virtual host).
 * @property string $docs The document root directory.
 * @property string $template The template directory.
 * @property string $location The URI location.
 * 
 * @property string $css The CSS location.
 * @property string $js The JS location.
 * @property string $img The image location.
 * 
 * @property array $locale Options for locale and gettext.
 * @property string $theme The default theme.
 * 
 * @property array $topmenu Optional top menu.
 * @property array $publisher Optional page publisher information.
 * 
 * @author Anders Lövgren (QNET/BMC CompDept)
 * @package UUP
 * @subpackage Site
 */
class Config
{

        /**
         * Configuration options.
         * @var array 
         */
        private $config;
        /**
         * The top directory.
         * @var string 
         */
        private $topdir;
        /**
         * The project directory.
         * @var string 
         */
        private $prjdir;
        
        /**
         * Constructor.
         * @param array|string $config Configuration options array or path to file.
         */
        public function __construct($config = null)
        {
                $this->topdir = realpath(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/..');
                $this->prjdir = realpath(__DIR__ . "/../../../..");

                // 
                // Get config settings:
                // 
                if (is_array($config)) {
                        // ignore
                } elseif (is_string($config)) {
                        $config = require($config);
                } elseif (defined('UUP_SITE_DEFAULTS')) {
                        $config = require('UUP_SITE_DEFAULTS');
                } elseif (filter_input(INPUT_ENV, 'UUP_SITE_DEFAULTS')) {
                        $config = require(filter_input(INPUT_ENV, 'UUP_SITE_DEFAULTS'));
                } elseif (($config = $this->locate("config/defaults.site"))) {
                        $config = require($config);
                } else {
                        throw new \Exception("Failed locate default.site");
                }

                if (filter_input(INPUT_COOKIE, 'theme')) {
                        $config['theme'] = filter_input(INPUT_COOKIE, 'theme');
                }
                if (filter_input(INPUT_GET, 'theme')) {
                        $config['theme'] = filter_input(INPUT_GET, 'theme');
                        setcookie("theme", $config['theme']);
                }
                if (!isset($config['theme'])) {
                        $config['theme'] = 'default';
                }

                if (!isset($config['root'])) {
                        $config['root'] = $this->topdir;
                }

                if (!isset($config['docs'])) {
                        $config['docs'] = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT');
                } elseif ($config['docs'][0] != '/') {
                        $config['docs'] = $this->locate($config['docs']);
                }

                if (!isset($config['template'])) {
                        $config['template'] = $this->locate("template");
                } elseif ($config['template'][0] != '/') {
                        $config['template'] = $this->locate($config['template']);
                }
                if (!isset($config['template'])) {
                        throw new \Exception("The template directory is missing");
                }

                if (!isset($config['location'])) {
                        $config['location'] = "/";
                }

                foreach (array('css', 'js', 'img') as $asset) {
                        if (!isset($config[$asset])) {
                                $config[$asset] = sprintf("%s/%s/%s", $config['location'], $asset, $config['theme']);
                        } elseif ($config[$asset][0] != '/') {
                                $config[$asset] = sprintf("%s/%s/%s", $config['location'], $config[$asset], $config['theme']);
                        }
                        if ($config[$asset][1] == '/') {
                                $config[$asset] = str_replace('//', '/', $config[$asset]);
                        }
                }

                $this->config = $config;
        }

        public function __get($name)
        {
                if (isset($this->config[$name])) {
                        return $this->config[$name];
                }
        }

        public function __set($name, $value)
        {
                $this->config[$name] = $value;
        }

        public function __isset($name)
        {
                return array_key_exists($name, $this->config);
        }

        private function locate($path)
        {
                foreach (array($this->topdir, $this->prjdir, __DIR__) as $test) {
                        if (($dest = realpath(sprintf("%s/%s", $test, $path)))) {
                                return $dest;
                        }
                }
        }

}
