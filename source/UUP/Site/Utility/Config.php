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

if (!defined('UUP_SITE_EXCEPT_LOG')) {
        /**
         * Log exception to error log.
         */
        define('UUP_SITE_EXCEPT_LOG', 1);
}
if (!defined('UUP_SITE_EXCEPT_SILENT')) {
        /**
         * Silent ignore reporting exceptions.
         */
        define('UUP_SITE_EXCEPT_SILENT', 2);
}
if (!defined('UUP_SITE_EXCEPT_DUMP')) {
        /**
         * Display exception using dump (not for production mode).
         */
        define('UUP_SITE_EXCEPT_DUMP', 4);
}
if (!defined('UUP_SITE_EXCEPT_BRIEF')) {
        /**
         * Display brief exception message.
         */
        define('UUP_SITE_EXCEPT_BRIEF', 8);
}
if (!defined('UUP_SITE_EXCEPT_STACK')) {
        /**
         * Include stack in output (not for production mode).
         */
        define('UUP_SITE_EXCEPT_STACK', 16);
}

/**
 * Site configuration class.
 *
 * @property string $name The site name.
 * 
 * @property string $root The top directory (virtual host).
 * @property string $docs The document root directory.
 * @property string $proj The project directory.
 * @property string $template The template directory.
 * @property string $location The URI location.
 * 
 * @property string $css The CSS location.
 * @property string $js The JS location.
 * @property string $img The image location.
 * @property string $font The font location.
 * 
 * @property array $locale Options for locale and gettext.
 * @property string $theme The default theme.
 * 
 * @property array $topmenu Optional top bar menu.
 * @property array $publish Optional publisher information.
 * @property array $headers Optional HTTP headers.
 * @property string $footer Optional footer file.
 * 
 * @property int $exception The exception reporting mode.
 * 
 * @method string css(string $filepath) Get CSS file location.
 * @method string stylesheet(string $filepath) Get CSS file location.
 * 
 * @method string font(string $filepath) Get font file location.
 * 
 * @method string image(string $filepath) Get image file location.
 * @method string img(string $filepath) Get image file location.
 * 
 * @method string js(string $filepath) Get javascript file location.
 * @method string javascript(string $filepath) Get javascript file location.
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
        private $_config;
        /**
         * The top directory.
         * @var string 
         */
        private $_topdir;
        /**
         * The project directory.
         * @var string 
         */
        private $_prjdir;
        /**
         * Cached configuration options.
         * @var array 
         */
        private static $_cached = null;

        /**
         * Constructor.
         * 
         * @param array|string $config Configuration options array or path to file.
         * @param boolean $verify Check required options.
         * @param boolean $cached Use cached config if present.
         */
        public function __construct($config = null, $verify = true, $cached = true)
        {
                if ($cached) {
                        $this->cached();
                }
                if (!isset($this->_config)) {
                        $this->detect($config);
                }
                if ($verify) {
                        $this->verify();
                }

                if (!isset($this->_topdir)) {
                        $this->_topdir = $this->_config['root'];
                }
                if (!isset($this->_prjdir)) {
                        $this->_prjdir = $this->_config['proj'];
                }
        }

        public function __get($name)
        {
                if (isset($this->_config[$name])) {
                        return $this->_config[$name];
                }
        }

        public function __set($name, $value)
        {
                $this->_config[$name] = $value;
        }

        public function __isset($name)
        {
                return array_key_exists($name, $this->_config);
        }

        public function __call($name, $arguments)
        {
                switch ($name) {
                        case 'css':
                        case 'stylesheet':
                                return $this->getCss($arguments[0]);
                        case 'font':
                                return $this->getFont($arguments[0]);
                        case 'image':
                        case 'img':
                                return $this->getImage($arguments[0]);
                        case 'js':
                        case 'javascript':
                                return $this->getJs($arguments[0]);
                }
        }

        /**
         * Use cached config if present.
         * @return boolean 
         */
        private function cached()
        {
                if (isset(self::$_cached)) {
                        $this->_config = self::$_cached;
                        return true;
                } else {
                        return false;
                }
        }

        /**
         * Detect configuration.
         * 
         * @param array|string $config Configuration options array or path to file.
         * @throws \Exception
         */
        private function detect($config)
        {
                $this->_topdir = realpath(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/..');
                $this->_prjdir = realpath(__DIR__ . "/../../../..");

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
                        die("Failed locate defaults.site (have you missed running uup-site.sh in application or site root?)");
                }

                if (!isset($config['root'])) {
                        $config['root'] = $this->_topdir;
                } else {
                        $this->_topdir = $config['root'];
                }

                if (!isset($config['proj'])) {
                        $config['proj'] = $this->_prjdir;
                } else {
                        $this->_prjdir = $config['proj'];
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

                if (!isset($config['location'])) {
                        $config['location'] = "/";
                }
                if (!isset($config['exception'])) {
                        $config['exception'] = UUP_SITE_EXCEPT_BRIEF;
                }

                if (filter_input(INPUT_COOKIE, 'theme')) {
                        $config['theme'] = filter_input(INPUT_COOKIE, 'theme');
                }
                if (filter_input(INPUT_GET, 'theme')) {
                        $config['theme'] = filter_input(INPUT_GET, 'theme');
                        setcookie("theme", $config['theme'], 0, $config['location']);
                }
                if (!isset($config['theme'])) {
                        $config['theme'] = 'default';
                }

                foreach (array('css', 'js', 'img', 'fonts') as $asset) {
                        if (!isset($config[$asset])) {
                                $config[$asset] = sprintf("%s/theme/%s/assets/%s", $config['location'], $config['theme'], $asset);
                        } elseif ($config[$asset][0] != '/') {
                                $config[$asset] = sprintf("%s/theme/%s/assets/%s", $config['location'], $config['theme'], $config[$asset]);
                        }
                        if ($config[$asset][1] == '/') {
                                $config[$asset] = str_replace('//', '/', $config[$asset]);
                        }
                }

                foreach (array('topmenu', 'publish', 'headers') as $key) {
                        if (!isset($config[$key])) {
                                $config[$key] = false;
                        }
                }

                if (!isset($config['footer'])) {
                        $config['footer'] = true;
                }
                if ($config['footer']) {
                        if (!is_string($config['footer'])) {
                                $config['footer'] = 'footer.inc';
                        }
                        if (!file_exists($config['footer'])) {
                                $config['footer'] = $this->locate($config['footer']);
                        }
                        if (!isset($config['footer'])) {
                                $config['footer'] = sprintf("%s/%s/footer.inc", $config['template'], $config['theme']);
                        }
                        if (!file_exists($config['footer'])) {
                                $config['footer'] = false;
                        }
                }

                if (isset($config['debug']) && $config['debug'] == true) {
                        error_log(print_r($config, true));
                }

                self::$_cached = $config;
                $this->_config = $config;
        }

        /**
         * Verify current config.
         * @throws \Exception
         */
        private function verify()
        {
                if (!isset($this->_config['template'])) {
                        die("The template directory is missing");
                }
        }

        /**
         * Locate file in some standard locations.
         * 
         * @param string $path The file name.
         * @return string
         */
        private function locate($path)
        {
                foreach (array($this->_topdir, $this->_prjdir, __DIR__) as $test) {
                        if (($dest = realpath(sprintf("%s/%s", $test, $path)))) {
                                return $dest;
                        }
                }
        }

        /**
         * Get asset (e.g. CSS).
         * @param string $type The asset type.
         * @param string $name The file name.
         * @return string
         */
        public function getAsset($type, $name)
        {
                return sprintf("%s/%s", $this->$type, $name);
        }

        /**
         * Get CSS file.
         * @param string $name The file name.
         * @return string
         */
        public function getCss($name)
        {
                return sprintf("%s/%s", $this->css, $name);
        }

        /**
         * Get Javascript file.
         * @param string $name The file name.
         * @return string
         */
        public function getJs($name)
        {
                return sprintf("%s/%s", $this->js, $name);
        }

        /**
         * Get image file.
         * @param string $name The file name.
         * @return string
         */
        public function getImage($name)
        {
                return sprintf("%s/%s", $this->img, $name);
        }

        /**
         * Get font file.
         * @param string $name The file name.
         * @return string
         */
        public function getFont($name)
        {
                return sprintf("%s/%s", $this->fonts, $name);
        }

        /**
         * Get config data.
         * @return array
         */
        public function getData()
        {
                return $this->_config;
        }

}
