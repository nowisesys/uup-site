<?php

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

namespace UUP\Site\Page\Migration;

use UUP\Site\Page\StandardPage;

/**
 * Transitional page support.
 * 
 * Don't use this class unless you need it. It provides support for migration
 * from an anchient template system using naked PHP function (e.g. print_body() 
 * and print_title()). 
 * 
 * @author Anders Lövgren (QNET/BMC CompDept)
 * @package UUP
 * @subpackage Site
 */
class TransitionalPage extends StandardPage
{

        /**
         * The page path.
         * @var string 
         */
        private $_page;
        /**
         * The body content.
         * @var string 
         */
        private $_body = "";
        /**
         * The page headers.
         * @var string 
         */
        private $_header = "";

        /**
         * Constructor.
         * @param string $page The transitional page path.
         */
        public function __construct($page)
        {
                $this->_page = $page;
                $this->capture();

                parent::__construct($this->_title);
        }

        public function printContent()
        {
                echo $this->_body;
        }

        public function printHeader()
        {
                echo $this->_header;
        }

        private function capture()
        {
                require_once($this->_page);
                ob_start();

                if (function_exists('print_headers')) {
                        print_headers();
                        $this->_header = ob_get_contents();
                        ob_clean();
                }
                if (function_exists('print_body')) {
                        print_body();
                        $this->_body = ob_get_contents();
                        ob_clean();
                }
                if (function_exists('print_title')) {
                        print_title();
                        $this->_title = ob_get_contents();
                        ob_clean();
                } else {
                        $this->_title = strtoupper(basename($this->_page, "php"));
                }

                ob_end_clean();
        }

}
