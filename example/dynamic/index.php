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

require_once(realpath(__DIR__ . '/../../vendor/autoload.php'));

use UUP\Site\Page\Web\StandardPage;

class IndexPage extends StandardPage
{

        public function __construct()
        {
                // 
                // Use welcome page template instead of default standard template.
                // 
                parent::__construct(__CLASS__);

                // 
                // Insert new menu section:
                // 
                $this->navmenu->insert(array(
                        'head' => _("Header 2"),
                        'data' => array(
                                _('Page 3') => 'page3.php',
                                _('Page 4') => 'page4.php'
                        )
                ));

                // 
                // Append to first menu section (by index):
                // 
                $this->navmenu->append(array(
                        _('Page 5') => 'page5.php',
                        _('Page 6') => 'page6.php'
                ));

                // 
                // Append to second menu section (by index):
                // 
                $this->navmenu->append(array(
                        _('Page 7') => 'page7.php',
                        _('Page 8') => 'page8.php'
                    ), 1);

                // 
                // Append to first menu section (by name):
                // 
                $this->navmenu->append(array(
                        _('Page 9')  => 'page9.php',
                        _('Page 10') => 'page10.php'
                    ), _("Header 1"));

                // 
                // Remove menu section:
                // 
                // $this->navmenu->remove(1);                   // Remove menu section by index.
                // $this->navmenu->remove("Header 2");          // Remove menu section by header.
                // $this->navmenu->remove("Header 2", "Page 4");// Remove menu item by header/name.
                
                // 
                // Delete all menu sections:
                // 
                // $this->navmenu->clear();
        }

        public function printContent()
        {
                echo "<h1>Mixed menu and template handling</h1>\n";
                echo "<p>All pages in this directory should display a navigation menu using the ";
                echo "<a href=\"standard.menu\">standard.menu</a> ";
                echo "file.</p>";

                echo "<p>This page demonstrate using insert() and append() methods on the ";
                echo "\$this->navmenu object to dynamic modify the navigation menu.</p>\n";

                if ($this->config->debug) {
                        echo "<pre>\n";
                        print_r($this->navmenu);
                        echo "</pre>\n";
                }
        }

}

$page = new IndexPage();
$page->render();
