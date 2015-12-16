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

require_once(realpath(__DIR__ . '/../../vendor/autoload.php'));

use UUP\Site\Page\StandardPage;

class IndexPage extends StandardPage
{

        public $sections;

        public function __construct()
        {
                // 
                // Use start page template instead of default standard template.
                // 
                parent::__construct(__CLASS__, "start");

                // 
                // Example of per page menus:
                // 
                $this->navmenu->append(array(
                        _('Page 1') => 'page1.php',
                        _('Page 2') => 'page2.php'
                ));

                $this->sections = array(
                        _('Introduction') => array(
                                'text'  => "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.",
                                'image' => 'http://shankman.com/images/imguploader/images/Introduction.png',
                                'link'  => 'page3.php'
                        ),
                        _('Products')     => array(
                                'text'  => "Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.",
                                'image' => 'http://www.prevention.com/sites/prevention.com/files/images/news/featured_images/beauty-product-bottles-TS-173643647-628.jpg',
                                'link'  => 'page4.php'
                        ),
                        _('Contact')      => array(
                                'text'  => "Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.",
                                'image' => 'http://www.rightsofolderpeople.org/wp-content/uploads/2013/12/contact-us.jpg',
                                'link'  => 'page5.php'
                        )
                );
        }

        public function printContent()
        {
                echo "<h1>Start page</h1>\n";
        }

}

$page = new IndexPage();
$page->render();
