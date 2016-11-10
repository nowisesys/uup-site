<?php

/*
 * Copyright (C) 2016 Anders Lövgren (Computing Department at BMC, Uppsala University).
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

use UUP\Site\Page\StandardPage;

class IndexPage extends StandardPage
{

        public function __construct()
        {
                parent::__construct(__CLASS__);

                $this->config->content = array(
                        'name'  => 'Using image',
                        'desc'  => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
                        'info'  => 'The introduction text for this part of the application or site',
                        'image' => 'https://cdn.pixabay.com/photo/2016/06/16/16/44/clock-1461689_640.jpg'
                );
        }

        public function printContent()
        {
                echo "<h1>Content specification test</h1>\n";

                echo "<p>This is a test of inline content specification (from array data). If you look at the page source (in the browser), you should see &lt;meta name=\"...\" content=\"...\"&gt; defined by the content.spec file.</p>\n";
                echo "<p>It sort of an demonstration of how content specification can be used in an <u>object oriented</u> approach in contrast to the <u>infrastructure mode</u> provided by using content.spec files.</p>\n";

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
