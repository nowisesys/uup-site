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

namespace UUP\Site\Page\Context;

use UUP\Site\Page\Context\Menu\SideMenu;
use UUP\Site\Page\Context\Menu\StandardMenu;
use UUP\Site\Page\Context\Menu\TopMenu;

/**
 * Container class for page menus.
 *
 * @property-read StandardMenu $nav The navigation menu.
 * @property-read TopMenu $top The top menu
 * @property-read SideMenu $side The sidebar menu.
 * 
 * @author Anders Lövgren (QNET/BMC CompDept)
 * @package UUP
 * @subpackage Site
 */
class Menus
{

        /**
         * @var array|boolean $topmeny The top menu. 
         */
        private $_topmenu;

        /**
         * Constructor.
         * @param array|boolean $topmeny The top menu. 
         */
        public function __construct($topmenu)
        {
                $this->_topmenu = $topmenu;
        }

        public function __get($name)
        {
                switch ($name) {
                        case 'nav':
                                $this->nav = $this->getNavMenu();
                                return $this->nav;
                        case 'top':
                                $this->top = $this->getTopMenu();
                                return $this->top;
                        case 'side':
                                $this->side = $this->getSideMenu();
                                return $this->side;
                }
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
                return new TopMenu($this->_topmenu);
        }

}
