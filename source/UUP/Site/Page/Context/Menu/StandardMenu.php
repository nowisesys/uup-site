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

namespace UUP\Site\Page\Context\Menu;

/**
 * Navigation menu support class.
 *
 * @author Anders Lövgren (QNET/BMC CompDept)
 * @package UUP
 * @subpackage Site
 */
class StandardMenu extends \ArrayObject
{

        /**
         * Constructor.
         * @param array|boolean $navmenu The top menu.
         */
        public function __construct($navmenu = false)
        {
                if ($navmenu != false) {
                        if (is_array($navmenu)) {
                                $menus = array(new MenuData($navmenu));
                        } else {
                                $menus = array();

                                if (file_exists("standard.menu")) {
                                        $menus[] = new MenuData(include("standard.menu"));
                                } else {
                                        $menus[] = false;
                                }
                                if (file_exists("../standard.menu")) {
                                        $menus[] = new MenuData(include("../standard.menu"), "..");
                                } else {
                                        $menus[] = false;
                                }
                                if (file_exists("../../standard.menu")) {
                                        $menus[] = new MenuData(include("../../standard.menu"), "../..");
                                } else {
                                        $menus[] = false;
                                }
                        }
                        if (count($menus) != 0) {
                                parent::__construct($menus);
                        }
                }
        }

}
