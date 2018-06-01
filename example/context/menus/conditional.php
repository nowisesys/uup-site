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

class ConditionalPage extends StandardPage
{

        public function __construct()
        {
                parent::__construct(__CLASS__);

                $this->config->navmenu = array(
                        'head' => 'Conditional',
                        'data' => array(
                                _("Public 1") => array(
                                        'href' => 'public.php'
                                ),
                                _("Secure 1") => array(
                                        'href' => 'protected.php',
                                        'auth' => true
                                ),
                                _("Secure 2") => array(
                                        'href' => 'protected.php',
                                        'auth' => 'true'
                                ),
                                _("Secure 3") => array(
                                        'href' => 'protected.php',
                                        'auth' => 'yes'
                                ),
                                _("Public 2") => array(
                                        'href' => 'public.php'
                                ),
                        )
                );
        }

        public function printContent()
        {
                echo "<h1>Conditional showing elements</h1>\n";
                echo "<p>Elements (i.e. menu links or divs) can be conditional hidden based on ";
                echo "whether caller is authenticated or not. Login or logout from this page ";
                echo "to see the effect.</p>\n";

                echo "<div auth=\"true\" class=\"w3-panel w3-blue\"><h3>Secure content</h3><p>This message should only be seen when user is logged on.</p></div>\n";
                echo "<div auth=\"false\" class=\"w3-panel w3-green\"><h3>Public content</h3><p>This message has 'auth=false' and should always be visible.</p></div>\n";

                echo "<p>The following 'boolean' values can be used as element attribute to flag ";
                echo "content that should only be displayed when user is logged on:</p>\n";
                echo "<ul>";
                echo "<li>auth='true'</li>\n";
                echo "<li>auth='yes'</li>\n";
                echo "<li>auth='1'</li>\n";
                echo "</ul>";

                echo "<h4>Example:</h4>\n";
                echo "<ul>";
                echo "<li>&lt;div auth=\"true\" class=\"w3-panel w3-blue\"&gt;Show this message if used is logged on&lt;div&gt;</li>\n";
                echo "</ul>";

                echo "<h4>Menu:</h4>\n";
                echo "<pre><code>\n";
                print_r($this->navmenu);
                echo "</code></pre>\n";
        }

}

$page = new ConditionalPage();
$page->render();
