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

require_once(realpath(__DIR__ . '/../../../vendor/autoload.php'));

use UUP\Site\Page\Web\StandardPage;

class IndexPage extends StandardPage
{

        public function __construct()
        {
                parent::__construct(__CLASS__);
        }

        public function printContent()
        {
                echo "<h1>Page topmenu</h1>\n";
                echo "<p>Examples on using page topmenus. As the name suggests, this menu is displayed at top of page and usually contains common links that should be available no matter which page in the site the user is currently on.</p>\n";

                if (!$this->config->headers) {
                        echo "<p>Use of topbar menus is <u>disabled</u> in the configuration, but these examples will dynamic enable support for testing purposes.</p>\n";
                }
                
                printf("<ul>\n");
                printf("<li><a href=\"file\">Use topmenu defined in file system</a></li>\n");
                printf("<li><a href=\"object\">Use topmenus defined inside the class</a></li>\n");
                printf("</ul>\n");
                
                echo "<p><pre><code>\n";
                print_r($this->sidebar);
                echo "</code></pre></p>\n";
        }

}

$page = new IndexPage();
$page->render();
