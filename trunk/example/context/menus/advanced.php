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

use UUP\Site\Page\Web\StandardPage;

class AdvancedPage extends StandardPage
{

        public function __construct()
        {
                parent::__construct(__CLASS__);

                $this->config->topmenu = array(
                        // Use default target:
                        _('T1') => '::topmenu1.php',
                        // // Use target top2-id:
                        _('T2') => ':topmenu2-id:topmenu2.php',
                        // Equivalent to T2
                        _('T3') => array(
                                'href' => $this->config->url('topmenu3.php'),
                                'dest' => 'topmenu3-id'
                        ),
                        // Equivalent to T3 (already relocated site root)
                        _('T4') => array(
                                'href' => $this->config->url('@topmenu4.php'),
                                'dest' => 'topmenu4-id'
                        ),
                        // Relocated to site root (i.e. public):
                        _('R1') => '@/topmenu5.php',
                        // Relative current script:
                        _('R2') => 'topmenu6.php',
                        // Use virtual host as root:
                        _('R3') => '/topmenu7.php',
                        // Combined example of dynamic update and relocation:
                        _('C1') => ':topmenu8-id:@/topmenu8.php',
                        // Test extern link:
                        _('E1') => 'http://www.bmc.uu.se'
                );

                $this->config->navmenu = array(
                        'head' => _('Navmenu'),
                        'data' => array(
                                _('N1') => ':navmenu1-id:navmenu1.php',
                                _('N2') => '@/navmenu2.php'
                        )
                );
                $this->config->sidebar = array(
                        _('Sidebar') => array(
                                _('S1') => ':sidebar1-id:sidebar1.php',
                                _('S2') => '@/sidebar2.php'
                        )
                );
        }

        public function printContent()
        {
                echo "<h1>Advanced menus</h1>\n";
                echo "<p>Examples of defining site root relocated and dynamic content replace menus. Use of menus has been forced on by this class for demonstration purposes.</p>\n";
                echo "<p>Because links are rendered the same for all kind of menus, only the topbar menu contains the full set of test cases. Use developer tools in the web browser to inspect.</p>\n";

                echo "<p><pre><code>\n";
                print_r($this->topmenu);
                print_r($this->navmenu);
                print_r($this->sidebar);
                echo "</code></pre></p>\n";
        }

}

$page = new AdvancedPage();
$page->render();
