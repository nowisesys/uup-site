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

namespace UUP\Site\Page;

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
 * See example/routed for more information.
 * 
 * Setup:
 * -----------
 * 
 * <code>
 * RewriteEngine On
 * RewriteCond %{REQUEST_FILENAME} !-d
 * RewriteCond %{REQUEST_FILENAME} !-f
 * RewriteRule ^(.*)$ /index.php?uri=$1 [QSA,L]
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
class RouterPage extends StandardPage
{

        /**
         * The requested page.
         * @var string 
         */
        private $page;
        /**
         * The class name.
         * @var string 
         */
        private $name;
        /**
         * The class name suffix.
         * @var string 
         */
        private $suffix = "Page";
        /**
         * The filename extension.
         * @var string 
         */
        private $ext = "php";
        /**
         * The class namespace.
         * @var string 
         */
        private $ns = "\\";

        /**
         * Constructor.
         * @param string $config Path to site config file.
         */
        public function __construct($config = null)
        {
                parent::__construct("Router", null, $config);

                $this->config->uri = filter_input(INPUT_GET, 'uri');
                $this->page = $this->getPage();
                $this->name = $this->getName();
        }

        public function printContent()
        {
                
        }

        /**
         * Handle the route request.
         * @throws \Exception
         */
        public function handle()
        {
                if (!file_exists($this->page)) {
                        throw new \Exception("Requested page don't exist");
                } else {
                        require($this->page);
                }

                if (!class_exists($this->name)) {
                        throw new \Exception("The requested class what not found");
                } else {
                        $page = new $this->name();
                        $page->render();
                }
        }

        /**
         * Set class name suffix.
         * @param string $suffix The class name suffix.
         */
        public function setSuffix($suffix)
        {
                $this->suffix = $suffix;
                $this->name = $this->getName();
        }

        /**
         * Set filename extension.
         * @param string $ext The filename extension.
         */
        public function setExtension($ext)
        {
                $this->ext = $ext;
                $this->page = $this->getPage();
        }

        /**
         * Set class namespace.
         * @param string $ns The namespace.
         */
        public function setNamespace($ns)
        {
                $this->ns = $ns;
                $this->name = $this->getName();
        }

        /**
         * Get class name.
         * @return string
         */
        private function getName()
        {
                $parts = explode('-', basename($this->config->uri));
                $parts = array_map('ucfirst', $parts);
                return sprintf("%s%s%s", $this->ns, implode('', $parts), $this->suffix);
        }

        /**
         * Get page name.
         * @return string
         */
        private function getPage()
        {
                return sprintf("%s.%s", $this->config->uri, $this->ext);
        }

}
