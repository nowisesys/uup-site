<?php

use UUP\Site\Page\Web\WelcomePage as ParentClass;

class WelcomePage extends ParentClass
{

        public $sections;

        public function __construct()
        {
                parent::__construct("Welcome Page");

                $this->sections = array(
                        _('Introduction') => array(
                                'text'  => "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.",
                                'image' => $this->config->getImage('introduction.jpeg'),
                                'link'  => 'page1.php'
                        ),
                        _('Products')     => array(
                                'text'  => "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.",
                                'image' => $this->config->getImage('products.jpeg'),
                                'link'  => 'page2.php'
                        ),
                        _('Contact')      => array(
                                'text'  => "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.",
                                'image' => $this->config->getImage('contact.jpeg'),
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

                include("partial/details.inc");
        }

}
