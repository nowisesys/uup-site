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

use UUP\Site\Page\Web\Component\Link;
use UUP\Site\Page\Web\StandardPage;

class IndexPage extends StandardPage
{

        public function __construct()
        {
                parent::__construct(__CLASS__);
        }

        public function printContent()
        {
                echo "<h1>DOM content replace</h1>\n";
                echo "<p>Use ':target-id:link' in standard menus to define menu links that fetches ";
                echo "content using AJAX requests and replaces the target-id content. If target-id ";
                echo "is missing, then 'page-content' is used as default target ID.</p>\n";

                echo "<p>If requested content contains script-tags, then they are added if onajax=\"add\"";
                echo "attribute is set and run if onajax=\"run\" is set (see script.php). Any script ";
                echo "fragment without onajax attribute is missing.</p>\n";

                echo "<p>Pages can programatically use the Link class to render links:</p>\n";

                echo "<ul>\n";
                $menus = include('standard.menu');
                foreach ($menus['data'] as $name => $source) {
                        echo "<li>";
                        $link = new Link($name, $source);
                        $link->render($this->config->location);
                        echo "</li>\n";
                }
                echo "</ul>\n";

                echo "<div id=\"custom-id\"></div>\n";

                echo "<p><pre><code>\n";
                print_r($this->menus);
                echo "</code></pre></p>\n";
        }

}

$page = new IndexPage();
$page->render();
