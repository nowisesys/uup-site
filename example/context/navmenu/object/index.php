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
                $this->config->navmenu = array(
                        'head' => _('Object Mode'),
                        'data' => array(
                                _('Page 3') => 'page3',
                                _('Page 4') => 'page4'
                        )
                );
        }

        public function printContent()
        {
                echo "<h1>Navigation menus</h1>\n";
                echo "<p>This is a test of using standard menus defined by page class. It demonstrate an object oriented approach on using navigation menus.</p>\n";
                echo "<p>Please notice that menus defined inside a class over-rules the navmenu config setting (in defaults.site). It will also completely disable scanning for standard.menu files in current directory and its parent directories.</p>\n";

                echo "<p><pre><code>\n";
                print_r($this->navmenu);
                print_r($this->menus);
                echo "</code></pre></p>\n";
        }

}

$page = new IndexPage();
$page->render();
