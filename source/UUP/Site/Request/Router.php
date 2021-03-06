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

namespace UUP\Site\Request;

use Exception;
use RuntimeException;
use UUP\Site\Page\Web\Migration\TransitionalPage;
use UUP\Site\Page\Web\StandardView;

/**
 * Request router page.
 * 
 * This class routes requests to a page that implements the view for requested URL. 
 * 
 * It supports conversion of dashed names to camel case. If the request is for the 
 * getting-started page, then the router tries to require the file getting-started.php 
 * containing a class named GetttingStartedPage.
 * 
 * The intended use of the router page class is inside a common index.php setup to 
 * handle all public pages request. The obvious benefit is that view pages don't have
 * to include the autoloader nor instantiate itself and then call render(). 
 * 
 * See example/routing for more information.
 * 
 * Setup:
 * -----------
 * 
 * <code>
 * RewriteEngine On
 * RewriteCond %{REQUEST_FILENAME} !-d
 * RewriteCond %{REQUEST_FILENAME} !-f
 * RewriteRule ^(.*)$ /dispatch.php?uri=$1 [QSA,L]
 * </code>
 * 
 * Notice:
 * -----------
 * 
 * The directory index options in the web server configuration might cause the view
 * to be called direct, bypassing the routing. This is a common problem if the view
 * page is named index.php and can be solved by changing the default filename extension
 * in the router:
 * 
 * <code>
 * $router->setExtension("phtml");
 * </code>
 * 
 * Other options of interest might be changing namespace from global or the class
 * name suffix:
 * 
 * <code>
 * $router->setNamespace("Application\\Views\\");
 * $router->setSuffix("View");
 * </code>
 * 
 * Config file:
 * -----------
 * 
 * If site configuration is in a non-standard location, then pass the path when creating
 * the router page object:
 * 
 * <code>
 * $config = "/etc/apache2/conf.d/localhost.uup-site.def";
 * $router = new RouterPage($config);
 * </code>
 * 
 * 
 * @author Anders Lövgren (Nowise Systems/BMC-IT, Uppsala University)
 * @package UUP
 * @subpackage Site
 */
class Router extends Handler
{

        /**
         * The global namespace.
         */
        const NAMESPACE_GLOBAL = "\\";
        /**
         * The default namespace (omit use).
         */
        const NAMESPACE_DEFAULT = false;

        /**
         * The request URI without params.
         * @var string 
         */
        private $_path;
        /**
         * The requested page (script name).
         * @var string 
         */
        private $_page;
        /**
         * The class name.
         * @var string 
         */
        private $_name;
        /**
         * The working directory.
         * @var string 
         */
        private $_root;
        /**
         * The class name suffix.
         * @var string 
         */
        private $_suffix = "Page";
        /**
         * The filename extension.
         * @var string 
         */
        private $_ext = "php";
        /**
         * The class namespace.
         * @var string 
         */
        private $_ns = self::NAMESPACE_DEFAULT;

        /**
         * Constructor.
         * @param string $config Path to site config file.
         */
        public function __construct($config = null)
        {
                parent::__construct($config);

                $this->config->uri = filter_input(INPUT_GET, 'uri', FILTER_SANITIZE_STRING);

                $this->_root = getcwd();
                $this->_path = $this->getPath();
                $this->_page = $this->getPage();
                $this->_name = $this->getName();
        }

        /**
         * Handle the route request.
         * @throws Exception
         */
        public function handle()
        {
                $this->profile->push("dispatch::handle");
                $this->profile->start();

                set_include_path(get_include_path() . PATH_SEPARATOR . sprintf("%s/admin", $this->config->proj));

                $this->routeLoadHandler($this->_page);
                $this->routeNextHandler($this->_page, $this->_name);

                $this->profile->stop();
        }

        /**
         * Load handler class.
         * 
         * Require requested page to get associated class defined. If $page is a 
         * simple view, then the class will be undefined.
         * 
         * @param string $page The script name.
         * @throws RuntimeException
         */
        private function routeLoadHandler($page)
        {
                if (!file_exists($page)) {
                        throw new RuntimeException(_("Requested page don't exist"));
                } elseif (!chdir(dirname($page))) {
                        throw new RuntimeException(_("Failed change to target route directory"));
                } else {
                        ob_start();
                        require($page);
                        ob_end_clean();
                }
        }

        /**
         * Use next handler class.
         * 
         * If requested class is defined, then instantiate it. Otherwise create a
         * wrapped object (handler).
         * 
         * @param string $page The script name.
         * @param string $name The class name.
         */
        private function routeNextHandler($page, $name)
        {
                if (class_exists($name)) {
                        $dest = new $name();
                        $this->routeNextRender($dest);
                } elseif (function_exists('print_body')) {
                        $dest = new TransitionalPage($page);
                        $this->routeNextRender($dest);
                } else {
                        $dest = new StandardView($this->getTitle(), $page);
                        $this->routeNextRender($dest);
                }
        }

        /**
         * Set class name suffix.
         * @param string $suffix The class name suffix.
         */
        public function setSuffix($suffix)
        {
                $this->_suffix = $suffix;
                $this->_name = $this->getName();
        }

        /**
         * Set filename extension.
         * @param string $ext The filename extension.
         */
        public function setExtension($ext)
        {
                $this->_ext = $ext;
                $this->_page = $this->getPage();
        }

        /**
         * Set class namespace.
         * @param string $ns The namespace.
         */
        public function setNamespace($ns)
        {
                $this->_ns = $ns;
                $this->_name = $this->getName();
        }

        /**
         * Set working directory.
         * @param string $dir The root directory.
         */
        public function setDirectory($dir)
        {
                $this->_root = $dir;
                $this->_page = $this->getPage();
        }

        /**
         * Get request URI without params.
         * @return string
         */
        private function getPath()
        {
                if (($pos = strpos($this->config->uri, "?")) !== false) {
                        return substr($this->config->uri, 0, $pos);
                } else {
                        return $this->config->uri;
                }
        }

        /**
         * Get class name.
         * @return string
         */
        private function getName()
        {
                if ($this->useNamespace()) {
                        $camelized = $this->getQualified($this->_path);
                } else {
                        $camelized = $this->getCamelized(basename($this->_path));
                }

                return sprintf(
                        "%s%s%s",
                        $this->_ns,
                        $camelized,
                        $this->_suffix
                );
        }

        /**
         * Should namespace be used?
         * 
         * Returns true if namespace is set to an string not equal to global 
         * namespace. If namespace is anything thats empty (i.e. null or false), 
         * then this method will return false.
         *
         * @return bool
         */
        private function useNamespace(): bool
        {
                return empty($this->_ns) == false;
        }

        /**
         * Get semi-qualified class name.
         * 
         * Derive an partial qualified class name from script path components by
         * applying camel case transformation.
         *
         * @param string $path The script path.
         * @return string
         */
        private function getQualified(string $path): string
        {
                $parts = explode('/', $path);

                foreach ($parts as $index => $name) {
                        $parts[$index] = $this->getCamelized($name);
                }

                return implode('\\', $parts);
        }

        /**
         * Transform to camel case.
         * 
         * Split input string on '-' and apply upper case string on each part.
         * 
         * @param string $name The input string.
         * @return string
         */
        private function getCamelized(string $name): string
        {
                $parts = explode('-', $name);
                $parts = array_map('ucfirst', $parts);

                return implode('', $parts);
        }

        /**
         * Get page name.
         * @return string
         */
        private function getPage()
        {
                return sprintf("%s/%s.%s", $this->_root, $this->_path, $this->_ext);
        }

        /**
         * Get page title from view name.
         * @return string
         */
        private function getTitle()
        {
                $dirs = array_reverse(explode('/', $this->_path));

                if (count($dirs) == 1) {
                        $name = $dirs[0];
                } elseif ($dirs[0] == 'index') {
                        $name = $dirs[1];
                } else {
                        $name = $dirs[0];
                }

                $part = explode('-', $name);
                $part = array_map('ucfirst', $part);

                return implode(' ', $part);
        }

        public function render()
        {
                trigger_error("Don't call render() on router, use handler() instead.");
                $this->handle();
        }

        /**
         * Render routed page handler.
         * @param Handler $page The target request handler.
         */
        private function routeNextRender($page)
        {
                $this->profile->push("dispatch::render");
                $this->profile->start();

                $page->params = new Params($this->config->docs);
                $page->params->setFile($this->_page);
                $page->params->setPath($this->_page);

                $page->render();

                $this->profile->stop();
        }
}
