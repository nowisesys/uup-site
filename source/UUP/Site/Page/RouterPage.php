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
 * page is named index.php
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
         * Constructor.
         */
        public function __construct()
        {
                parent::__construct("Router");

                $this->config->uri = filter_input(INPUT_GET, 'uri');
                $this->page = $this->getPage();
                $this->name = $this->getName();
                
                error_log(print_r($this, true));
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

        public function printContent()
        {
                
        }

        /**
         * Get class name.
         * @return string
         */
        private function getName()
        {
                $parts = explode('-', basename($this->config->uri));
                $parts = array_map('ucfirst', $parts);
                return sprintf("%sPage", implode('', $parts));
        }

        /**
         * Get page name.
         * @return string
         */
        private function getPage()
        {
                return sprintf("%s.php", $this->config->uri);
        }

}
