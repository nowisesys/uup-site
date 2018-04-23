<?php

/*
 * Copyright (C) 2015-2017 Anders Lövgren (QNET/BMC CompDept).
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
 * @author Anders Lövgren (QNET/BMC CompDept)
 * @package UUP
 * @subpackage Site
 */
class Router extends Handler
{

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
        private $_ns = "\\";

        /**
         * Constructor.
         * @param string $config Path to site config file.
         */
        public function __construct($config = null)
        {
                parent::__construct($config);

                $this->config->uri = filter_input(INPUT_GET, 'uri');
                $this->_root = getcwd();
                $this->_page = $this->getPage();
                $this->_name = $this->getName();
        }

        /**
         * Handle the route request.
         * @throws Exception
         */
        public function handle()
        {
                set_include_path(get_include_path() . PATH_SEPARATOR . sprintf("%s/admin", $this->config->proj));

                if (!file_exists($this->_page)) {
                        throw new RuntimeException(_("Requested page don't exist"));
                } elseif (!chdir(dirname($this->_page))) {
                        throw new RuntimeException(_("Failed change to target route directory"));
                } else {
                        ob_start();
                        require($this->_page);
                        ob_end_clean();
                }
                
                if (class_exists($this->_name)) {
                        $page = new $this->_name();
                        $this->routed($page);
                } elseif (function_exists('print_body')) {
                        $page = new TransitionalPage($this->_page);
                        $this->routed($page);
                } else {
                        $page = new StandardView($this->getTitle(), $this->_page);
                        $this->routed($page);
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
         * Get class name.
         * @return string
         */
        private function getName()
        {
                $parts = explode('-', basename($this->config->uri));
                $parts = array_map('ucfirst', $parts);
                return sprintf("%s%s%s", $this->_ns, implode('', $parts), $this->_suffix);
        }

        /**
         * Get page name.
         * @return string
         */
        private function getPage()
        {
                return sprintf("%s/%s.%s", $this->_root, $this->config->uri, $this->_ext);
        }

        /**
         * Get page title from view name.
         * @return string
         */
        private function getTitle()
        {
                $dirs = array_reverse(explode('/', $this->config->uri));

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
                $this->handle();
        }

        /**
         * Route page request to page.
         * @param Handler $page The target request handler.
         */
        private function routed($page)
        {
                $page->params = new Params($this->config->docs);
                $page->params->setFile($this->_page);
                $page->params->setPath($this->_page);

                $page->render();
        }

}
