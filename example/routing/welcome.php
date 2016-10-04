<?php

use UUP\Site\Page\WelcomePage as StartPage;

/*
 * Copyright (C) 2016 Anders Lövgren (Computing Department at BMC, Uppsala University).
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

class WelcomePage extends StartPage
{

        public $sections;

        public function __construct()
        {
                parent::__construct("Welcome Page");

                $this->sections = array(
                        _('Introduction') => array(
                                'text'  => "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.",
                                'image' => $this->config->getImage('introduction.png'),
                                'link'  => 'page3.php'
                        )
                );
        }

        public function printContent()
        {
                printf("<h1>The welcome page</h1>");
                printf("<p>This page class derives from start page instead of the generic standard page class.</p>\n");
                printf("<p>The main content is provided by this class, while the jumbo above is defined by the welcome page template. ");
                printf("Start pages usually has centered content and provides on or more sub sections that might be oriented in columns at bottom or in rows higher up on the page.</p>\n");
                printf("<p>An site or application typical has a single page (index) using the welcome page template.</p>\n");
        }

}
