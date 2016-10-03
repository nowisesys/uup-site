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
 * See example/transitional for demo.
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
         * Output deprecation warning to error log.
         * @var bool
         */
        private $_warn;
        /**
         * The body content.
         * @var string 
         */
        private $_body = false;
        /**
         * The page headers.
         * @var string 
         */
        private $_header = false;

        /**
         * Constructor.
         * @param string $page The transitional page path.
         * @param bool $warn Output deprecation warning to error log.
         */
        public function __construct($page, $warn = false)
        {
                $this->_page = $page;
                $this->_warn = $warn;

                $this->capture();

                parent::__construct($this->_title);
        }

        /**
         * Check if body content was found.
         * @return bool
         */
        public function hasContent()
        {
                return is_string($this->_body);
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
                if (!file_exists($this->_page)) {
                        return;
                }
                if (strstr(file_get_contents($this->_page), 'print_body') === false) {
                        return;
                }

                ob_start();
                include_once($this->_page);

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

                if ($this->_warn) {
                        error_log("Notice: The script $this->_page is rendered using deprecated template system. Consider migrate from transitional rendering to standard page class (pure OOP) See 'uup-site.sh --migrate' and 'admin/migrate.php'.");
                }

                ob_end_clean();
        }

}
