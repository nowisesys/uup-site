<?php

/*
 * Copyright (C) 2015-2017 Anders Lövgren (Nowise Systems/BMC-IT, Uppsala University).
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

require_once(realpath(__DIR__ . '/../../vendor/autoload.php'));

use UUP\Site\Page\Web\StandardPage;

class IndexPage extends StandardPage
{

        public function __construct()
        {
                parent::__construct(__CLASS__);
        }

        public function printContent()
        {
                echo "<h1>Context test</h1>\n";
                echo "<p>This is a test of menus and publish info gathered from files included in this directory and parent directories.</p>\n";

                printf("<ul>Context examples:\n");
                printf("<li><a href=\"content\">Use content specification</a></li>\n");
                printf("<li><a href=\"headers\">Output custom HTML headers</a></li>\n");
                printf("<li><a href=\"publish\">Provide and display publisher information</a></li>\n");
                printf("</ul>\n");

                printf("<ul>Menus examples:\n");
                printf("<li><a href=\"navmenu\">Navigation menu</a></li>\n");
                printf("<li><a href=\"topmenu\">Topbar menu</a></li>\n");
                printf("<li><a href=\"sidebar\">Including sidebar menus</a></li>\n");
                printf("<li><a href=\"menus\">Advanced menus</a></li>\n");
                printf("</ul>\n");

                printf("<ul>Other examples:\n");
                printf("<li><a href=\"tree\">Infrastructure context test</a></li>\n");
                printf("<li><a href=\"replace\">DOM content replace using target ID</a></li>\n");
                printf("</ul>\n");
        }

}

$page = new IndexPage();
$page->render();
