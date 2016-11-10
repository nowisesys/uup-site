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

require_once(realpath(__DIR__ . '/../../../vendor/autoload.php'));

use UUP\Site\Page\StandardPage;

class IndexPage extends StandardPage
{

        public function __construct()
        {
                parent::__construct(__CLASS__);
        }

        public function printContent()
        {
                echo "<h1>Sidebar menu</h1>\n";
                echo "<p>Examples on using sidebar menus. The location of sidebar is defined by each theme that can also chose to hide the sidebar on any kind devices. A sensable approach could by to merge the sidebar menu with the navigation menu combined with a separator.</p>\n";

                printf("<ul>\n");
                printf("<li><a href=\"file\">Use sidebar menus defined in file system</a></li>\n");
                printf("<li><a href=\"object\">Use sidebar menus defined inside the class</a></li>\n");
                printf("</ul>\n");
                
                echo "<p><pre><code>\n";
                print_r($this->sidemenu);
                echo "</code></pre></p>\n";
        }

}

$page = new IndexPage();
$page->render();
