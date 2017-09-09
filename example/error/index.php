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

// 
// Test exception handling at page render time.
// 

class IndexPage extends StandardPage
{

        public function __construct()
        {
                parent::__construct(__CLASS__);
        }

        public function printContent()
        {
                echo "<h1>Error handling</h1>\n";
                echo "<p>Examples testing error handling. The exception should be displayed on ";
                echo "an error page with various level of details depending on the system ";
                echo "configuration being used.</p>\n";

                echo "<h4>Examples:</h4>\n";

                printf("<ul>\n");
                printf("<li><a href=\"init.php\">Exception thrown in constructor</a></li>\n");
                printf("<li><a href=\"page1.php\">Passing exception to error page constructor</a></li>\n");
                printf("<li><a href=\"page2.php\">Call exception handler on error page object</a></li>\n");
                printf("<li><a href=\"page3.php\">Call exception handler on standard page object (empty exception)</a></li>\n");
                printf("<li><a href=\"page4.php\">Call exception handler on standard page object (full exception)</a></li>\n");
                printf("<li><a href=\"page5.php\">Call exception handler on standard page object (nested exceptions)</a></li>\n");
                printf("<li><a href=\"render.php\">Throw exception at render time</a></li>\n");
                printf("</ul>\n");

                echo "<h4>Current config:</h4>\n";
                echo "<ul>\n";
                if ($this->config->exception & UUP_SITE_EXCEPT_LOG) {
                        echo "<li>UUP_SITE_EXCEPT_LOG (write exception to error log)</li>\n";
                }
                if ($this->config->exception & UUP_SITE_EXCEPT_SILENT) {
                        echo "<li>UUP_SITE_EXCEPT_SILENT (error report is suppressed)</li>\n";
                }
                if ($this->config->exception & UUP_SITE_EXCEPT_BRIEF) {
                        echo "<li>UUP_SITE_EXCEPT_BRIEF (only display exception class and message)</li>\n";
                }
                if ($this->config->exception & UUP_SITE_EXCEPT_STACK) {
                        echo "<li>UUP_SITE_EXCEPT_STACK (show stack trace)</li>\n";
                }
                if ($this->config->exception & UUP_SITE_EXCEPT_DUMP) {
                        echo "<li>UUP_SITE_EXCEPT_DUMP (full disclosure of exception)</li>\n";
                }
        }

}

$page = new IndexPage();
$page->render();
