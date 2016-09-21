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

namespace UUP\Site\Page;

/**
 * Page for displaying error.
 *
 * @author Anders Lövgren (QNET/BMC CompDept)
 * @package UUP
 * @subpackage Site
 */
class ErrorPage extends StandardPage
{

        /**
         * The trapped exception.
         * @var \Exception 
         */
        private $_exception;

        public function __construct($exception)
        {
                parent::__construct("Error");
                $this->_exception = $exception;
                ob_clean();
        }

        public function printContent()
        {
                printf("<h1>Oops, something went wrong!</h1>\n");

                if ($this->config->exception & UUP_SITE_EXCEPT_LOG) {
                        error_log(print_r($this->_exception, true));
                }

                if ($this->config->exception & UUP_SITE_EXCEPT_SILENT) {
                        printf("An exception occured, but error reporing has been suppressed by the system manager.");
                        return;
                }
                if ($this->config->exception & UUP_SITE_EXCEPT_BRIEF) {
                        printf("<b>%s:</b> %s<br/>\n", get_class($this->_exception), $this->_exception->getMessage());
                }
                if ($this->config->exception & UUP_SITE_EXCEPT_STACK) {
                        $stack = $this->_exception->getTraceAsString();
                        printf("<b>Stack:</b> %s<br/>\n", $stack);
                }
                if ($this->config->exception & UUP_SITE_EXCEPT_DUMP) {
                        printf("<p><pre><code>\n");
                        print_r($this->_exception);
                        printf("</code></pre></p>\n");
                }
        }

}
