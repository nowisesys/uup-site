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

class MyPage extends StandardPage
{
        public $sections;

        public function __construct()
        {
                // 
                // Use welcome page template instead of default standard template:
                // 
                // parent::__construct(__CLASS__, "welcome");   // alt. 1
                // $this->setTemplate("welcome");               // alt. 2
                //                 
                parent::__construct(__CLASS__, "welcome");
                
                $this->sections = array(
                        _('Introduction') => array(
                                'text'  => "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.",
                                'image' => $this->config->getImage('introduction.jpeg'),
                                'link'  => 'page3.php'
                        ),
                        _('Products')     => array(
                                'text'  => "Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.",
                                'image' => $this->config->getImage('products.jpeg'),
                                'link'  => 'page4.php'
                        ),
                        _('Contact')      => array(
                                'text'  => "Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.",
                                'image' => $this->config->getImage('contact.jpeg'),
                                'link'  => 'page5.php'
                        )
                );
        }

        public function printContent()
        {                
                echo "<h1>Welcome page</h1>\n";
                
                echo "<p>This page should be rendered using the 'welcome' template. ";
                echo "Switching template can be useful if the page should adapt its UI ";
                echo "at runtime.</p>\n";
                
                echo "<p>There are two different ways to switch between the rendering ";
                echo "template being used:</p>\n";
                echo "<code><pre>\n";
                echo "// Inside constructor:\n";
                echo "parent::__construct(\$title, \"welcome\");\n";
                echo "\n";
                echo "// During execution:\n";
                echo "\$this->setTemplate(\"welcome\");\n";
                echo "</pre></code>\n";
                echo "<p>The template should normally be set before the rendering chain is ";
                echo "being started. This means that once \$this->render() has been called ";
                echo "the template can't be switched.</p>\n";
        }

}

$page = new MyPage();
$page->render();
