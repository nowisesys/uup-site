<?php

/*
 * Copyright (C) 2017 Anders Lövgren (QNET/BMC CompDept).
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

class SeparatorPage extends StandardPage
{

        public function __construct()
        {
                parent::__construct(__CLASS__);

                $this->config->navmenu = array(
                        'head' => _('Menu'),
                        'data' => array(
                                _('Link 1')    => 'page1.php',
                                _('Link 2')    => 'page2.php',
                                false, // Separator
                                _('Link 3')    => 'page3.php',
                                1              => false, // Separator
                                _('Link 4')    => 'page4.php',
                                _('Separator') => false, // Separator
                                _('Link 5')    => 'page5.php',
                                Link::SEPARATOR, // Separator
                                _('Link 6')    => 'page6.php',
                                '<hr style="border-color: green; border-width: thin; border-style: dashed; margin: 0px">', // Separator
                                _('Link 7')    => 'page7.php',
                                array(
                                        'style' => 'border-color: red; border-width: thin; border-style: dashed; margin: 0px' // Separator
                                ),
                                _('Link 8')    => 'page8.php'
                        )
                );
        }

        public function printContent()
        {
                echo "<h3>Menu separators</h3>\n";
                echo "<p>An separator helps to group links in menus. They should have no ";
                echo "user interaction or visual feedback on mouse hover.</p>\n";

                echo "<p>The standard separator has class=\"menu-separator\" assigned to its ";
                echo "horizontal row element so a custom style can be applied if required.</p>\n";

                echo "<p>Any numeric key or key having value == false in data is detected and ";
                echo "rendered as a menu separator. For readability, it's recommended to use ";
                echo "either false (boolean) or Link::SEPARATOR when declaring menu separators.</p>\n";

                echo "<pre><code>\n";
                print_r($this->navmenu);
                echo "</code></pre>\n";
        }

}

$page = new SeparatorPage();
$page->render();
