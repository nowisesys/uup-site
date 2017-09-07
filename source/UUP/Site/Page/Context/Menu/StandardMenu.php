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
                                parent::__construct(array_filter($menus));
                        }
                }
        }

        /**
         * Insert new menu data.
         * 
         * <code>
         * $page->navmenu->insert(array(
         *      'head' => 'Name',
         *      'data' => array(
         *              'Link1' => 'file1.php', 
         *              'Link2' => 'file2.php'
         *      )
         * ));
         * </code>
         * 
         * @param array $value The sub-menu data definition.
         */
        public function insert($value)
        {
                parent::append(new MenuData($value));
        }

        /**
         * Append menu data.
         * 
         * Use this method to append data to an existing menu. The head argument 
         * is either an index or string (the menu name). The default is to append 
         * data to first menu.
         * 
         * <code>
         * $page->navmenu->append(array(
         *      'Link1' => 'file1.php', 
         *      'Link2' => 'file2.php'
         * ));
         * </code>
         * 
         * Another options is pass an complete menu array. The array is the used
         * as the $data and $head argument:
         * 
         * <code>
         * $page->navmenu->append(array(
         *      'head' => 'Name',
         *      'data' => array(
         *              'Link1' => 'file1.php', 
         *              'Link2' => 'file2.php'
         *      )
         * ));
         * </code>
         * 
         * The previous example is the same as this example:
         * 
         * <code>
         * $page->navmenu->append(array(
         *      'Link1' => 'file1.php', 
         *      'Link2' => 'file2.php'
         * ), 'Name');
         * </code>
         * 
         * @param type $data
         * @param type $head
         */
        public function append($data, $head = 0)
        {
                if (isset($data['head']) && isset($data['data'])) {
                        $data = $data['data'];
                        $head = $data['head'];
                }

                if (is_numeric($head)) {
                        if (isset($this[$head])) {
                                $menu = $this[$head];
                                $menu->data = array_merge($menu->data, $data);
                        }
                } elseif (is_string($head)) {
                        foreach ($this as $menu) {
                                if ($menu->name != $head) {
                                        continue;
                                }
                                $menu->data = array_merge($menu->data, $data);
                        }
                }
        }

}
