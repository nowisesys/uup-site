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

require_once(realpath(__DIR__ . '/../../../../vendor/autoload.php'));

use UUP\Site\Page\Web\StandardPage;

class IndexPage extends StandardPage
{

        public function __construct()
        {
                parent::__construct(__CLASS__);
                $this->config->content = true;
        }

        public function printContent()
        {
                echo "<h1>Content specification test</h1>\n";
                echo "<p>This is a test of content specification (i.e. from content.spec). If you look at the page source (in the browser), you should see &lt;meta name=\"...\" content=\"...\"&gt; defined by the content.spec file.</p>\n";

                printf("<div class=\"w3-row-padding w3-padding-64 w3-container\">\n");
                printf("<div class=\"w3-content\">\n");

                printf("<div class=\"w3-twothird\">\n");
                printf("<h1>%s</h1>\n", $this->content->name);
                printf("<h5 class=\"w3-padding-32\">%s</h5>\n", $this->content->info);
                printf("<p class=\"w3-text-grey\">%s</p>\n", $this->content->desc);
                printf("</div>\n");

                printf("<div class=\"w3-third w3-center\">\n");
                printf("<img src=\"%s\">\n", $this->content->image);
                printf("</div>\n");

                printf("</div>\n");
                printf("</div>\n");
        }

}

$page = new IndexPage();
$page->render();
