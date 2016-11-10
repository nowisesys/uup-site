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

require_once(realpath(__DIR__ . '/../../../../vendor/autoload.php'));

use UUP\Site\Page\StandardPage;

class IndexPage extends StandardPage
{
        public function __construct()
        {
                parent::__construct(__CLASS__);
                $this->config->topmenu = true;
        }

        public function printContent()
        {
                echo "<h1>Page topmenu</h1>\n";
                echo "<p>This is a test of using top menus defined by topbar.menu file located in the file system. When using the default theme, you should now see the links appearing in the topbar menu.</p>\n";
                
                echo "<p><pre><code>\n";
                print_r($this->topmenu);
                echo "</code></pre></p>\n";
        }

}

$page = new IndexPage();
$page->render();
