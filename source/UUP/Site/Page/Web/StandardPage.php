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

namespace UUP\Site\Page\Web;

use UUP\Site\Page\Context\Content;
use UUP\Site\Page\Context\Menu\SideMenu;
use UUP\Site\Page\Context\Menu\StandardMenu;
use UUP\Site\Page\Context\Menu\TopMenu;
use UUP\Site\Page\Context\Menus;
use UUP\Site\Page\Context\Publisher;
use UUP\Site\Request\Handler as RequestHandler;
use UUP\Site\Utility\Content\Navigator;
use UUP\Site\Utility\Fortune;

/**
 * Standard page for this site.
 * 
 * @property-read Publisher $publisher Page publisher information.
 * @property-read Menus $menus Page menus.
 * @property-read Content $content Page content specification.
 * @property-read Fortune $fortune The fortune cookie.
 * @property-read Navigator $navigator The page/path navigator.
 * 
 * @property-read TopMenu $topmenu The top menu.
 * @property-read StandardMenu $navmenu The navigation (standard) menu.
 * @property-read SideMenu $sidebar The sidebar menu.
 * 
 * @property-read string $title The page title.
 * 
 * @author Anders Lövgren (QNET/BMC CompDept)
 * @package UUP
 * @subpackage Site
 */
abstract class StandardPage extends RequestHandler implements PageTemplate
{

        /**
         * The page title. 
         * @var string
         */
        protected $_title;
        /**
         * The template used for page rendering.
         * @var string  
         */
        private $_template;

        /**
         * Constructor.
         * @param string $title The page title.
         * @param string $template The output formatting.
         * @param string $config The defaults.site configuration file.
         */
        public function __construct($title, $template = "standard", $config = null)
        {
                if (ob_get_level() == 0) {
                        ob_start();
                } else {
                        ob_end_clean();
                        ob_start();
                }

                $this->_title = $title;
                $this->_template = $template;

                parent::__construct($config);
        }

        public function __get($name)
        {
                switch ($name) {
                        case 'publisher':
                                $this->publisher = $this->getPublisher();
                                return $this->publisher;
                        case 'menus':
                                $this->menus = $this->getMenus();
                                return $this->menus;
                        case 'content':
                                $this->content = $this->getContent();
                                return $this->content;
                        case 'fortune':
                                $this->fortune = $this->getFortune();
                                return $this->fortune;
                        case 'navigator':
                                $this->navigator = $this->getNavigator();
                                return $this->navigator;
                        case 'topmenu':
                                return $this->menus->top;
                        case 'navmenu':
                                return $this->menus->nav;
                        case 'sidebar':
                                return $this->menus->side;
                        case 'title':
                                return $this->_title;
                }

                return parent::__get($name);
        }

        public function __set($name, $value)
        {
                switch ($name) {
                        case 'publisher':
                                $this->publisher = $value;
                                break;
                        case 'menus':
                                $this->menus = $value;
                                break;
                        case 'content':
                                $this->content = $value;
                                break;
                        case 'fortune':
                                $this->fortune = $value;
                                break;
                        case 'navigator':
                                $this->navigator = $value;
                                break;
                        default:
                                parent::__set($name, $value);
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
                if (isset($this->_template)) {
                        // 
                        // Inject commonly used variables:
                        // 
                        $config = $this->config;
                        $session = $this->session;

                        // 
                        // Set site name for translation:
                        // 
                        $config->site = filter_input(INPUT_SERVER, 'SERVER_NAME');

                        // 
                        // Load template for UI rendering:
                        // 
                        include(sprintf("%s/%s/%s.ui", $this->config->template, $this->config->theme, $this->_template));
                } else {
                        $this->printContent();
                }
        }

        /**
         * Redirect peer to destination.
         * 
         * @param string $dest The target URL (relative, absolute or extern).
         */
        final public function redirect($dest)
        {
                header(sprintf("Location: %s", $this->config->url($dest)));
                exit(0);
        }

        /**
         * The exception handler.
         * @param \Exception $exception The exception to report.
         */
        public function onException($exception)
        {
                $page = new ErrorPage($exception);
                $page->render();
        }

        /**
         * Set render template.
         * @param string $template The template name.
         */
        public function setTemplate($template)
        {
                $this->_template = $template;
        }

        /**
         * Get page menus.
         * @return Menus
         */
        public function getMenus()
        {
                return new Menus($this->config->topmenu, $this->config->navmenu, $this->config->sidebar);
        }

        /**
         * Get page publisher information.
         * 
         * The information is defined by publish.inc is page directory or global publisher
         * info from template/publish.inc. If no publisher file was found, then the content
         * from the site config is returned.
         * 
         * @return Publisher
         */
        public function getPublisher()
        {
                return new Publisher($this->config->template, $this->config->publish);
        }

        /**
         * Get custom content specification.
         * @return Content
         */
        public function getContent()
        {
                return new Content($this->config->template, $this->config->content);
        }

        /**
         * Get fortune cookie (message of the day) object.
         * @return Fortune
         */
        public function getFortune()
        {
                return new Fortune($this->config->fortune);
        }

        /**
         * Get navigator for this page path.
         * @return Navigator
         */
        public function getNavigator()
        {
                return new Navigator($this->params->path, $this->config->url(""));
        }

}
