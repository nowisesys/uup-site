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

use UUP\Site\Page\Context\Content;
use UUP\Site\Page\Context\Headers;
use UUP\Site\Page\Context\Menu\SideMenu;
use UUP\Site\Page\Context\Menu\StandardMenu;
use UUP\Site\Page\Context\Menu\TopMenu;
use UUP\Site\Page\Context\Menus;
use UUP\Site\Page\Context\Publisher;
use UUP\Site\Utility\Config;
use UUP\Site\Utility\Locale;

/**
 * Standard page for this site.
 * 
 * @property-read Publisher $publisher Page publisher information.
 * @property-read Menus $menus Page menus.
 * @property-read Headers $headers Custom HTTP headers.
 * @property-read Content $content Page content specification.
 * 
 * @property-read TopMenu $topmenu The top menu.
 * @property-read StandardMenu $navmenu The navigation (standard) menu.
 * @property-read SideMenu $sidebar The sidebar menu.
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
        protected $_title;
        /**
         * The template used for page rendering.
         * @var string  
         */
        private $_template;
        /**
         * The site configuration object.
         * @var Config 
         */
        public $config;
        /**
         * The locale settings object.
         * @var Locale 
         */
        public $locale;

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

                $this->_title = $title;
                $this->_template = $template;

                $this->config = new Config($config);
                $this->locale = new Locale($this->config);
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
                        case 'headers':
                                $this->headers = $this->getHeaders();
                                return $this->headers;
                        case 'content':
                                $this->content = $this->getContent();
                                return $this->content;
                        case 'topmenu':
                                return $this->menus->top;
                        case 'navmenu':
                                return $this->menus->nav;
                        case 'sidebar':
                                return $this->menus->side;
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
                include(sprintf("%s/%s/%s.ui", $this->config->template, $this->config->theme, $this->_template));
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

        /**
         * Set render template.
         * @param string $template The template name.
         */
        public function setTemplate($template)
        {
                $this->_template = $template;
        }

        /**
         * Get site configuration.
         * @return Config
         */
        public function getConfig()
        {
                return $this->config;
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
         * Get custom HTTP headers.
         * @return Headers
         */
        public function getHeaders()
        {
                return new Headers($this->config->headers);
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
         * Get page title.
         * @return string
         */
        public function getTitle()
        {
                return $this->_title;
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
                if ($this->config->headers) {
                        echo "\n";
                        foreach ($this->headers as $tag => $attributes) {
                                foreach ($attributes as $attr) {
                                        echo "\t\t<$tag";
                                        foreach ($attr as $key => $val) {
                                                echo " $key=\"$val\"";
                                        }
                                        echo " />\n";
                                }
                        }
                }
                if ($this->config->content) {
                        echo "\n";
                        foreach ($this->content as $key => $val) {
                                if ($key == 'name') {
                                        printf("<meta name=\"application-name\" content=\"%s\">\n", $val);
                                } elseif ($key == 'info') {
                                        printf("<meta name=\"description\" content=\"%s\">\n", $val);
                                } elseif ($key == 'tags') {
                                        printf("<meta name=\"keywords\" content=\"%s\">\n", implode(',', $val));
                                }
                        }
                }
        }

}
