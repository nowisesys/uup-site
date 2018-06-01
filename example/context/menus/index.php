<?php

/*
 * Copyright (C) 2017 Anders Lövgren (Nowise Systems/BMC-IT, Uppsala University).
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
                echo "<h1>More on menus</h1>\n";
                echo "<p>These examples demonstrate more complex areas on menus, like javascript ";
                echo "menus that replaces page content without reloding the page.</p>\n";

                echo "<ul>\n";
                echo "<li><a href=\"separator.php\">Define menu separator.</a></li>\n";
                echo "<li><a href=\"advanced.php\">Content replace menus.</a></li>\n";
                echo "<li><a href=\"conditional.php\">Hide content when not authenticated.</a></li>\n";
                echo "</ul>\n";

                echo "<p>Menus are defined by its header and data. The data is normally an array ";
                echo "of name => link pairs, but the array values (link) are not restricted to being ";
                echo "strings. An array containing keys mapping standard attribute names is ";
                echo "another possibility:</p>\n";

                $menu = array(
                        'head' => _('Menu'),
                        'data' => array(
                                _('Link 1') => array(
                                        'onclick' => "open_content(event, 'files')"
                                ),
                                _('Link 2') => ':target-id:script.php',
                                _('Link 3') => array(
                                        'href'  => 'script.php',
                                        'title' => 'Some description text',
                                        'style' => 'color: green; text-decoration: underline'
                                ),
                                _('Link 4') => array(
                                        'href'    => '#',
                                        'onclick' => 'window.location = script.php',
                                        'class'   => 'w3-button w3-blue'
                                )
                        )
                );

                echo "<pre><code>\n";
                print_r($menu);
                echo "</code></pre>\n";
        }

}

$page = new IndexPage();
$page->render();
