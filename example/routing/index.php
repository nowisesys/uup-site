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

use UUP\Site\Page\Web\StandardPage;

class IndexPage extends StandardPage
{

        public function __construct()
        {
                parent::__construct("Index");
        }

        public function printContent()
        {
                echo "<h1>Using page routing</h1>\n";

                echo "<p>Locations under this directory uses a router to locate related pages/views ";
                echo "and render them. Preferable the routing is setup in the web server instead ";
                echo "of relying on an .htaccess file.</p>\n";
                
                echo "<p>Some example links:</p>\n";
                echo "<ul>\n";
                echo "<li><a href=\"welcome\">welcome</a></li>\n";
                echo "<li><a href=\"about\">about</a></li>\n";
                echo "<li><a href=\"test/\">index</a></li>\n";
                echo "<li><a href=\"test/getting-started\">getting-started</a></li>\n";
                echo "<li><a href=\"test/view\">view</a></li>\n";
                echo "<li><a href=\"error\">error</a></li>\n";
                echo "</ul>\n";
        }

}
