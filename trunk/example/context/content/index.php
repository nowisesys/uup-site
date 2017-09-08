<?php

/*
 * Copyright (C) 2016-2017 Anders Lövgren (Computing Department at BMC, Uppsala University).
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
                echo "<h1>Content specification test</h1>\n";

                if ($this->config->content) {
                        echo "<p>This is a test of content specification (i.e. from content.spec).</p>\n";
                } else {
                        echo "<p>Use of content specification is <u>disabled</u> in the configuration, but these examples will dynamic enable support for content specification.</p>\n";
                }

                printf("<ul>\n");
                printf("<li><a href=\"image\">Show page using image attribute</a></li>\n");
                printf("<li><a href=\"video\">Show page using video attribute</a></li>\n");
                printf("<li><a href=\"inline\">Show page using inline content specification</a></li>\n");
                printf("</ul>\n");
        }

}

$page = new IndexPage();
$page->render();
