<?php

/*
 * Copyright (C) 2015-2017 Anders Lövgren (Nowise Systems/BMC-IT, Uppsala University).
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

use DomainException;
use Exception;

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
if (!defined('UUP_SITE_EXCEPT_CODE')) {
        /**
         * Display exception code.
         */
        define('UUP_SITE_EXCEPT_CODE', 32);
}
if (!defined('UUP_SITE_EXCEPT_DEVELOP')) {
        /**
         * Development mode error reporting (not for production mode).
         */
        define('UUP_SITE_EXCEPT_DEVELOP', \
            UUP_SITE_EXCEPT_DUMP | UUP_SITE_EXCEPT_STACK | \
            UUP_SITE_EXCEPT_CODE | UUP_SITE_EXCEPT_BRIEF);
}
if (!defined('UUP_SITE_EXCEPT_ALL')) {
        /**
         * Full exception reporting (development + error logging).
         */
        define('UUP_SITE_EXCEPT_ALL', \
            UUP_SITE_EXCEPT_DEVELOP | UUP_SITE_EXCEPT_LOG);
}

/**
 * Site configuration class.
 *
 * @property-read array $data The config data.
 *
 * @property-read string $request The request URL.
 * @property-read bool $session Session is enabled.
 * @property-read bool $debug Request debug is enabled.
 *
 * @property string $site The site address.
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
 * @property-read array $edit The edit options.
 * @property-read array $auth The auth options.
 *
 * @property array $tools The toolbox options.
 * @property array $locale Options for locale and gettext.
 * @property string $theme The default theme.
 *
 * @property bool|array $topmenu Optional top bar menu.
 * @property bool|array $navmenu Optional navigation menu.
 * @property bool|array $sidebar Optional sidebar menu.
 * @property bool|array $publish Optional publisher information.
 * @property bool|array $headers Optional HTTP headers.
 * @property bool|array $content Optional content specification.
 * @property bool|string $footer Optional footer file.
 * @property bool|string $fortune Optional fortune cookie.
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
 * @method string url(string $dest) Get target URL.
 *
 * @author Anders Lövgren (Nowise Systems/BMC-IT, Uppsala University)
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
        private $_root;
        /**
         * The project directory.
         * @var string
         */
        private $_proj;
        /**
         * The documents directory.
         * @var string
         */
        private $_docs;
        /**
         * Directories to detect files in.
         * @var array
         */
        private $_subdirs = array();
        /**
         * Cached configuration options.
         * @var array
         */
        private static $_cached = null;

        /**
         * Constructor.
         *
         * @param array|string $config Configuration options array or path to file.
         * @param bool $verify Check required options.
         * @param bool $cached Use cached config if present.
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

                if (!isset($this->_root)) {
                        $this->_root = $this->_config['root'];
                }
                if (!isset($this->_docs)) {
                        $this->_docs = $this->_config['docs'];
                }
                if (!isset($this->_proj)) {
                        $this->_proj = $this->_config['proj'];
                }
        }

        public function __get($name)
        {
                if ($name == 'data') {
                        return $this->_config;
                } elseif (isset($this->_config[$name])) {
                        return $this->_config[$name];
                }
        }

        public function __set($name, $value)
        {
                if ($name == 'data') {
                        $this->_config = $value;
                } else {
                        $this->_config[$name] = $value;
                }
        }

        public function __isset($name)
        {
                if ($name == 'data') {
                        return isset($this->_config);
                } else {
                        return array_key_exists($name, $this->_config);
                }
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
                        case 'url':
                                return $this->getUrl($arguments[0]);
                }
        }

        /**
         * Use cached config if present.
         * @return bool
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
         * @throws Exception
         */
        private function detect($config)
        {
                //
                // Detecting the correct config is crucial for proper functioning, but has
                // shown to be a rather complex task due to support for both install using
                // standalone package and composer deploy.
                //
                // Theres also possible that bootstraping is used, where multiple virtual hosts
                // or web applications are setup by running uup-site.sh from a common installation
                // of uup-site in PHP's shared directory (usually /usr/share/php).
                //
                // Add to this that directories can be symlinked or aliased in web server
                // config. We try to make intelligent guess for correct location and probe
                // for the config directory in these possible locations.
                //
                // The best solution is if all pages uses the dispather with the config
                // location hard coded in routing. In this case the detection is rather
                // simple and a cheap operation.
                //
                //
                // Set root directory:
                //
                if (is_string($config)) {
                        $this->_root = self::find("config", $config, false);
                        $this->_docs = self::find("public", $config, true);
                        $this->_proj = realpath(__DIR__ . "/../../../..");
                } else {
                        $this->_root = self::find("config", realpath(filter_input(INPUT_SERVER, 'SCRIPT_FILENAME')), false);
                        $this->_docs = self::find("public", realpath(filter_input(INPUT_SERVER, 'SCRIPT_FILENAME')), true);
                        $this->_proj = realpath(__DIR__ . "/../../../..");
                }

                //
                // Add pathes to search directories:
                //
                $this->_subdirs[] = $this->_root;
                $this->_subdirs[] = $this->_proj;
                $this->_subdirs[] = dirname(getcwd());

                $this->_subdirs = array_values(array_unique($this->_subdirs));

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

                if (!isset($config['request'])) {
                        if (filter_input(INPUT_SERVER, 'SERVER_PORT') == 80 ||
                            filter_input(INPUT_SERVER, 'SERVER_PORT') == 443) {
                                $config['request'] = sprintf(
                                    "%s://%s%s", filter_input(INPUT_SERVER, 'REQUEST_SCHEME'), filter_input(INPUT_SERVER, 'SERVER_NAME'), filter_input(INPUT_SERVER, 'REQUEST_URI')
                                );
                        } else {
                                $config['request'] = sprintf(
                                    "%s://%s:%d%s", filter_input(INPUT_SERVER, 'REQUEST_SCHEME'), filter_input(INPUT_SERVER, 'SERVER_NAME'), filter_input(INPUT_SERVER, 'SERVER_PORT'), filter_input(INPUT_SERVER, 'REQUEST_URI')
                                );
                        }
                }

                if (!isset($config['site'])) {
                        $config['site'] = filter_input(INPUT_SERVER, 'SERVER_NAME');
                }
                if (!isset($config['name'])) {
                        $config['name'] = 'Default site name';
                }

                if (!isset($config['root'])) {
                        $config['root'] = $this->_root;
                } else {
                        $this->_root = $config['root'];
                }

                if (!isset($config['docs'])) {
                        $config['docs'] = $this->_docs;
                } else {
                        $this->_docs = $config['root'];
                }

                if (!isset($config['proj'])) {
                        $config['proj'] = $this->_proj;
                } else {
                        $this->_proj = $config['proj'];
                }

                if (!isset($config['template'])) {
                        $config['template'] = $this->locate("template");
                } elseif ($config['template'][0] != '/') {
                        $config['template'] = $this->locate($config['template']);
                }

                if (!isset($config['location'])) {
                        $config['location'] = "/";
                } elseif ($config['location'] != "/") {
                        $config['location'] = "/" . trim($config['location'], '/') . "/";
                }

                if (!isset($config['exception'])) {
                        $config['exception'] = UUP_SITE_EXCEPT_BRIEF;
                }
                if (!isset($config['polyfill'])) {
                        $config['polyfill'] = true;
                }

                if (filter_has_var(INPUT_COOKIE, 'theme')) {
                        $config['theme'] = filter_input(INPUT_COOKIE, 'theme', FILTER_SANITIZE_STRING);
                }
                if (filter_has_var(INPUT_GET, 'theme')) {
                        $config['theme'] = filter_input(INPUT_GET, 'theme', FILTER_SANITIZE_STRING);
                        setcookie("theme", $config['theme'], 0, $config['location']);
                }
                if (!isset($config['theme'])) {
                        $config['theme'] = 'default';
                }

                foreach (array('css', 'js', 'img', 'fonts') as $asset) {
                        if (!isset($config[$asset])) {
                                $config[$asset] = sprintf("%stheme/%s/assets/%s", $config['location'], $config['theme'], $asset);
                        } elseif ($config[$asset][0] != '/') {
                                $config[$asset] = sprintf("%stheme/%s/assets/%s", $config['location'], $config['theme'], $config[$asset]);
                        }
                        if ($config[$asset][1] == '/') {
                                $config[$asset] = str_replace('//', '/', $config[$asset]);
                        }
                }

                if (!isset($config['navmenu'])) {
                        $config['navmenu'] = true;      // enabled by default
                }
                if (!isset($config['sidebar'])) {
                        $config['sidebar'] = true;      // enabled by default
                }
                foreach (array('topmenu', 'publish', 'headers', 'content', 'session', 'debug', 'fortune', 'tools', 'auth', 'edit') as $key) {
                        if (!isset($config[$key])) {
                                $config[$key] = false;
                        }
                }

                $tools = array(
                        'tools' => array(
                                'home'      => false,
                                'auth'      => false,
                                'edit'      => false,
                                'search'    => false,
                                'translate' => false
                        ),
                        'auth'  => array(
                                'start'  => false,
                                'logon'  => '/auth/logon',
                                'logoff' => '/auth/logoff',
                                'config' => $config['proj'] . '/config/auth.inc',
                                'sso'    => true
                        ),
                        'edit'  => array(
                                'view' => '/edit/view',
                                'ajax' => '/edit/ajax',
                                'user' => array('webmaster'),
                                'host' => $config['site']
                        )
                );

                foreach ($tools as $tool => $data) {
                        if ($config[$tool]) {
                                if (!is_array($config[$tool])) {
                                        $config[$tool] = $data;
                                } else {
                                        $config[$tool] = array_merge($data, $config[$tool]);
                                }
                        }
                }

                if (!$config['tools']) {
                        $config['tools'] = $tools['tools'];
                }
                if ($config['tools']['auth'] || $config['tools']['edit']) {
                        $config['session'] = true;      // requires session support
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

                self::$_cached = $config;
                $this->_config = $config;
        }

        /**
         * Verify current config.
         * @throws Exception
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
                foreach ($this->_subdirs as $test) {
                        if (($dest = realpath(sprintf("%s/%s", $test, $path)))) {
                                return $dest;
                        }
                }
        }

        /**
         * Find parent directory.
         *
         * Uses the $script path to find the parent directory containing $subdir. This
         * method makes the assumption that by moving up in directory tree starting
         * from $script, we will find the $sibdir.
         *
         * @param string $subdir The subdir name.
         * @param string $script The script path.
         * @param bool $included Include subdir in outcome.
         * @return string
         */
        private static function find($subdir, $script, $included = false)
        {
                $pieces = explode("/", $script);

                while (array_pop($pieces)) {
                        $parent = implode("/", $pieces);
                        $target = sprintf("%s/%s", $parent, $subdir);

                        if (file_exists($target)) {
                                return $included ? $target : $parent;
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

        /**
         * Get target URL.
         *
         * Call this method to generate URL's relative to site location. If the
         * $extern argument is true, then returned URL contains schema, host and
         * port. Use $prefix to disable scheme, host and port detection.
         *
         * <code>
         * $config->getUrl();                           // Get location URL (i.e. -> /uup-site)
         * $config->getUrl("http://www.google.se");     // An extern URL
         * $config->getUrl("/assets/site.css");         // Relative virtual host
         * $config->getUrl("path/page");                // Relative site location (i.e. -> /uup-site/path/page)
         * $config->getUrl("path/page", true);          // For extern use (i.e. -> http://localhost/uup-site/path/page)
         * </code>
         *
         * @param string $dest The target URL (relative, absolute or extern).
         * @param bool $extern Generate URL with schema, host and port.
         * @param string $prefix Define prefix to use (i.e. https://www.example.com).
         * @return string
         * @throws DomainException
         */
        public function getUrl($dest, $extern = false, $prefix = "")
        {
                if ($extern == true && $prefix == "") {
                        if (filter_input(INPUT_SERVER, 'SERVER_PORT') == 80 ||
                            filter_input(INPUT_SERVER, 'SERVER_PORT') == 443) {
                                $prefix = sprintf(
                                    "%s://%s", filter_input(INPUT_SERVER, 'REQUEST_SCHEME'), filter_input(INPUT_SERVER, 'SERVER_NAME')
                                );
                        } else {
                                $prefix = sprintf(
                                    "%s://%s:%d", filter_input(INPUT_SERVER, 'REQUEST_SCHEME'), filter_input(INPUT_SERVER, 'SERVER_NAME'), filter_input(INPUT_SERVER, 'SERVER_PORT')
                                );
                        }
                }

                if (empty($dest)) {
                        return sprintf("%s%s", $prefix, $this->location);
                } elseif (!($comp = parse_url($dest))) {
                        throw new DomainException(_("Invalid URL $dest for redirect"));
                } elseif (isset($comp['scheme'])) {
                        return sprintf("%s", $dest);
                } elseif (!isset($comp['path'])) {
                        return sprintf("%s%s%s", $prefix, $this->location, $dest);
                } elseif ($dest[0] == '/') {
                        return sprintf("%s%s", $prefix, $dest);
                } elseif ($dest[0] == '@') {
                        return sprintf("%s%s%s", $prefix, $this->location, substr($dest, 1));
                } elseif ($this->location != "/") {
                        return sprintf("%s%s%s", $prefix, $this->location, $dest);
                } else {
                        return sprintf("%s/%s", $prefix, $dest);
                }
        }

}
