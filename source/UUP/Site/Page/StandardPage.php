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

use UUP\Site\Page\Context\StandardMenu;
use UUP\Site\Page\Context\PublishInfo;
use UUP\Site\Page\Context\SideMenu;
use UUP\Site\Page\Context\TopMenu;
use UUP\Site\Utility\Config;

/**
 * Standard page for this site.
 * 
 * @property-read StandardMenu $navmenu The navigation menu.
 * @property-read TopMenu $topmenu The top menu
 * @property-read SideMenu $sidemenu The sidebar menu.
 * @property-read PublishInfo $publish The publish information.
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
        public $config;

        /**
         * Constructor.
         * @param string $title The page title.
         * @param string $template The output formatting.
         * @param string $config The defaults.site configuration file.
         */
        public function __construct($title, $template = "standard", $config = null)
        {
                set_exception_handler(array($this, 'exception'));

                if (ob_get_level() == 0) {
                        ob_start();
                }

                $this->title = $title;
                $this->template = $template;
                $this->config = new Config($config);
        }

        public function __get($name)
        {
                switch ($name) {
                        case 'navmenu':
                                $this->navmenu = $this->getNavMenu();
                                return $this->navmenu;
                        case 'topmenu':
                                $this->topmenu = $this->getTopMenu();
                                return $this->topmenu;
                        case 'sidemenu':
                                $this->sidemenu = $this->getSideMenu();
                                return $this->sidemenu;
                        case 'publish':
                                $this->publish = $this->getPublishInfo();
                                return $this->publish;
                }
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

        /**
         * The exception handler.
         * @param \Exception $exception The exception to report.
         */
        public function exception($exception)
        {
                $page = new ErrorPage($exception);
                $page->render();
        }

        public function getConfig()
        {
                return $this->config;
        }

        /**
         * Get navigation menu.
         * 
         * Return content of menu files (standard.menu) in current directory and two levels up. 
         * @return StandardMenu
         */
        public function getNavMenu()
        {
                return new StandardMenu();
        }

        /**
         * Get sidebar menu.
         * 
         * The sidebar is typical a menu used to decorate the current page with links
         * to related pages, e.g. related projects. Sidebar menu files are named sidebar.menu.
         * 
         * @return SideMenu
         */
        public function getSideMenu()
        {
                return new SideMenu();
        }

        /**
         * Get topbar menu.
         * 
         * The topbar menu is usually output at top of page and contains context 
         * independent links. Returns content from site config if defined, otherwise
         * from a topmenu.menu in current directory.
         * 
         * @return TopMenu
         */
        public function getTopMenu()
        {
                return new TopMenu($this->config->topmenu);
        }

        /**
         * Get page publisher information.
         * 
         * The information is defined by publish.inc is page directory or global publisher
         * info from template/publish.inc. If no publisher file was found, then the content
         * from the site config is returned.
         * 
         * @return PublishInfo
         */
        public function getPublishInfo()
        {
                return new PublishInfo($this->config->template, $this->config->publish);
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
