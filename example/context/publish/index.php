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
                echo "<h1>Publisher information</h1>\n";
                echo "<p>Examples on providing publisher informatation for display on-site. This information can be defined both in files (publish.inc) or by the page class.</p>\n";
                echo "<p>It's up to the theme to decide on whether this information should be displayed at all and where in page it will appear in that case. In most cases it will be rendered at bottom of page in some dimmed color.</p>\n";

                if (!$this->config->publish) {
                        echo "<p>Use of publisher information is <u>disabled</u> in the configuration, but these examples will dynamic enable support for testing purposes.</p>\n";
                }

                printf("<ul>\n");
                printf("<li><a href=\"file\">Display publisher information defined in file system</a></li>\n");
                printf("<li><a href=\"object\">Display publisher information defined inside the class</a></li>\n");
                printf("</ul>\n");

                echo "<p><pre><code>\n";
                print_r($this->publisher);
                echo "</code></pre></p>\n";
        }

}

$page = new IndexPage();
$page->render();
