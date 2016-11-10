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
                echo "<h1>Custom HTML headers</h1>\n";
                echo "<p>Examples on output custom header in generated HTML. Can be used both with external headers file (headers.inc) or header data (array) defined by the page class. Headers can also be defined in the configuration file (defaults.site)</p>\n";

                if (!$this->config->headers) {
                        echo "<p>Use of custom HTML headers is <u>disabled</u> in the configuration, but these examples will dynamic enable support for testing purposes.</p>\n";
                }

                printf("<ul>\n");
                printf("<li><a href=\"file\">Use headers defined in file system</a></li>\n");
                printf("<li><a href=\"object\">Use headers defined inside the class</a></li>\n");
                printf("</ul>\n");
                
                echo "<p><pre><code>\n";
                print_r($this->headers);
                echo "</code></pre></p>\n";
        }

}

$page = new IndexPage();
$page->render();
