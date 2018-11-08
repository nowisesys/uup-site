<?php

/*
 * Copyright (C) 2017 Anders Lövgren (Nowise Systems/BMC-IT, Uppsala University).
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

namespace UUP\Site\Utility\Content;

use UUP\Site\Page\Web\StandardPage;
use UUP\Site\Utility\Config;

/**
 * Runtime config class.
 * 
 * Help theme template files to decide which UI components that should be 
 * displayed based on logon status and system config.
 * 
 * @property-read bool $home Show home icon.
 * @property-read bool $edit Show edit icon.
 * @property-read bool $auth Show auth icon.
 * @property-read bool $translate Show gooele translate icon.
 * @property-read bool $search Show site search icon.
 * 
 * @property-read bool $topmenu Show topbar menu.
 * @property-read bool $navmenu Show navigation menu.
 * 
 * @author Anders Lövgren (Nowise Systems/BMC-IT, Uppsala University)
 */
class Runtime
{

        /**
         * Constructor.
         * @param StandardPage $page The requested page.
         */
        public function __construct($page)
        {
                // 
                // Set values from config:
                // 
                $this->setupTools($page->config->tools);
                $this->setupMenus($page->config);

                // 
                // Set values from runtime dependencies:
                // 
                if ($this->edit) {
                        $this->edit = $page->session->authenticated();
                }
                if ($this->topmenu) {
                        $this->topmenu = count($page->topmenu) > 0 && $page->template != "welcome";
                }
                if ($this->navmenu) {
                        $this->navmenu = count($page->navmenu) > 0;
                }
        }

        /**
         * Setup icons for tools.
         * @param array $tools The tools config.
         */
        private function setupTools($tools)
        {
                foreach (array_keys($tools) as $tool) {
                        $this->$tool = boolval($tools[$tool]);
                }
        }

        /**
         * Setup icons for menus.
         * @param Config $config The site config.
         */
        private function setupMenus($config)
        {
                $this->topmenu = $config->topmenu !== false;
                $this->navmenu = $config->navmenu !== false;
        }

}
