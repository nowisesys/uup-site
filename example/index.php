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

require_once(realpath(__DIR__ . '/../vendor/autoload.php'));

use UUP\Site\Page\Web\StandardPage;

class IndexPage extends StandardPage
{

        public function __construct()
        {
                parent::__construct(__CLASS__);
        }

        public function printContent()
        {
                echo "<h1>Examples</h1>\n";
                echo "<p>The example directory contains demonstration of some features of the nowise/uup-site package.</p>\n";

                $menu = array(
                        _("Context") => array(
                                "href"  => "context",
                                "title" => _("Work with context (i.e. menus, meta data and dynamic content)")
                        ),
                        _("Dynamic") => array(
                                "href"  => "dynamic",
                                "title" => _("Dynamic menus, template and runtime detection")
                        ),
                        _("Error")   => array(
                                "href"  => "error",
                                "title" => _("Error and exception handling")
                        ),
                        _("Routing") => array(
                                "href"  => "routing",
                                "title" => _("Page routing")
                        ),
                        _("Secure")  => array(
                                "href"  => "secure",
                                "title" => _("Use protected pages (require logon)")
                        ),
                        _("Views")   => array(
                                "href"  => "view",
                                "title" => _("Render standard and secure views")
                        ),
                );

                printf("<ul>\n");
                foreach ($menu as $name => $data) {
                        printf("<li><a href=\"%s\">%s</a></li>\n", $data['href'], $data['title']);
                }
                printf("</ul>\n");

                echo "<h4>Getting started</h4>\n";
                echo "<p>\n";
                echo "A good starting point is to examine the examples on <a href=\"routing\">routing</a> ";
                echo "and <a href=\"context/menus/conditional.php\">conditional content</a>. For ";
                echo "building single page applications, checkout the ";
                echo "<a href=\"context/replace/\">dynamic update</a> example.</a>\n";
                echo "</p>\n";
                
                echo "<h4>Using views or pages</h4>\n";
                echo "<p>When building an site, use views (HTML-fragments) and setup routing. Use ";
                echo "context files (i.e. menus and publisher). Consider enabling the online page ";
                echo "editor.</p>\n";
                
                echo "<p>For building web applications, use pages (controllers) and conditional ";
                echo "load views. Consider defining base classes for pages for different parts ";
                echo "of the application that loads required javascripts.</p>\n";
                
                echo "<h4>Secure contents</h4>\n";
                echo "<p>Access to content can be secured static using an secure page or view ";
                echo "that requires authenticated session. To dynamic secure access, use a ";
                echo "standard page (controller) and ensure authetication calling authorize() ";
                echo "method is base class.</p>\n";
        }

}

$page = new IndexPage();
$page->render();
