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

use UUP\Site\Utility\Config;

/**
 * Standard page for this site.
 * 
 * @author Anders Lövgren (QNET/BMC CompDept)
 * @package UUP
 * @subpackage Site
 */
abstract class StandardPage implements TemplatePage
{

        /**
         * The page title. 
         * @var string
         */
        protected $title;
        /**
         * The template used for page rendering.
         * @var string  
         */
        private $template;
        /**
         * The site configuration object.
         * @var Config 
         */
        protected $config;

        /**
         * Constructor.
         * @param string $title The page title.
         * @param string $template The output formatting.
         * @param string $config The defaults.site configuration file.
         */
        public function __construct($title, $template = "standard", $config = null)
        {
                $this->title = $title;
                $this->template = $template;
                $this->config = new Config($config);

                ob_start();
        }

        /**
         * Render page to HTML.
         * 
         * This function should be called in the PHP script to render the page
         * using the selected UI template.
         */
        final public function render()
        {
                include(sprintf("%s/%s/%s.ui", $this->config->template, $this->config->theme, $this->template));
        }

        public function getConfig()
        {
                return $this->config;
        }

        /**
         * Get navigation menu.
         * 
         * Return content of menu files (standard.menu) in current directory and two levels up. 
         * Returns false if no menu files exist.
         * 
         * @return array|boolean
         */
        public function getNavMenu()
        {
                $menus = array();

                if (file_exists("standard.menu")) {
                        $menus[] = include("standard.menu");
                }
                if (file_exists("../standard.menu")) {
                        $menus[] = include("../standard.menu");
                }
                if (file_exists("../../standard.menu")) {
                        $menus[] = include("../../standard.menu");
                }

                if (count($menus) != 0) {
                        return $menus;
                } else {
                        return false;
                }
        }

        /**
         * Get sidebar menu.
         * 
         * The sidebar is typical a menu used to decorate the current page with links
         * to related pages, e.g. related projects. Sidebar menu files are named sidebar.menu.
         * 
         * Returns false if no sidebar exists in current page directory.
         * 
         * @return array|boolean
         */
        public function getSideMenu()
        {
                if (file_exists("sidebar.menu")) {
                        return include("sidebar.menu");
                } else {
                        return false;
                }
        }

        /**
         * Get topbar menu.
         * 
         * The topbar menu is usually output at top of page and contains context 
         * independent links. Returns content from site config if defined, otherwise
         * from a topmenu.menu in current directory. Returns false if topbar menu
         * is undefined.
         * 
         * @return array|boolean
         */
        public function getTopMenu()
        {
                if ($this->config->topmenu) {
                        return $this->config->topmenu;
                } elseif (file_exists("topmenu.menu")) {
                        return include("topmenu.menu");
                } else {
                        return false;
                }
        }

        /**
         * Get page publisher information.
         * 
         * The information is defined by publish.inc is page directory or global publisher
         * info from template/publish.inc. If no publisher file was found, then the content
         * from the site config is returned.
         * 
         * @return array
         */
        public function getPublishInfo()
        {
                if (file_exists("publish.inc")) {
                        return include("publish.inc");
                } elseif (file_exists(sprintf("%s/publish.inc", $this->config->template))) {
                        return include(sprintf("%s/publish.inc", $this->config->template));
                } else {
                        return $this->config->publisher;
                }
        }

        /**
         * Get page title.
         * @return string
         */
        public function getTitle()
        {
                return $this->title;
        }

        /**
         * Output extra HTML headers.
         * 
         * Override this method to output custom headers not provided by the UI theme.
         * This functionality is typical used for meta info related to pages in a 
         * sub directory.
         */
        public function printHeader()
        {
                
        }

}
