<?php

/*
 * Copyright (C) 2017 Anders Lövgren (QNET/BMC CompDept).
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

namespace UUP\Site\Page\Web\Component;

use UUP\Site\Page\Context\Menu\MenuData;

/**
 * HTML links component.
 * 
 * @property MenuData $menu The menu data.
 * 
 * @author Anders Lövgren (QNET/BMC CompDept)
 * @package UUP
 * @subpackage Site
 */
class Links
{

        /**
         * Constructor.
         * @param MenuData $menu The menu data.
         */
        public function __construct($menu)
        {
                $this->menu = $menu;
        }

        /**
         * Render all links.
         * @param string $location The site root location.
         */
        public function render($location)
        {
                if (isset($this->menu->name)) {
                        printf("<h3>%s</h3>\n", $this->menu->name);
                }
                if (isset($this->menu->data)) {
                        foreach ($this->menu->data as $name => $attr) {
                                $link = new Link($name, $attr);
                                $link->render($location);
                        }
                }
        }

}
