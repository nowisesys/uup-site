<?php

/*
 * Copyright (C) 2015-2017 Anders Lövgren (Nowise Systems/BMC-IT, Uppsala University).
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

namespace UUP\Site\Page\Web;

use Exception;
use UUP\Site\Page\Web\StandardPage;
use const UUP_SITE_EXCEPT_BRIEF;
use const UUP_SITE_EXCEPT_DUMP;
use const UUP_SITE_EXCEPT_LOG;
use const UUP_SITE_EXCEPT_SILENT;
use const UUP_SITE_EXCEPT_STACK;
use const UUP_SITE_EXCEPT_CODE;

/**
 * Page for displaying error.
 *
 * @author Anders Lövgren (Nowise Systems/BMC-IT, Uppsala University)
 * @package UUP
 * @subpackage Site
 */
class ErrorPage extends StandardPage
{

        /**
         * The trapped exception.
         * @var Exception 
         */
        private $_exception;

        /**
         * Constructor.
         * @param Exception $exception The exception object.
         */
        public function __construct($exception)
        {
                if (ob_get_level() != 0) {
                        ob_end_clean();
                }

                parent::__construct(_("Error"));

                $this->_exception = $exception;
        }

        public function printContent()
        {
                printf("<h1>%s</h1>\n", _("Oops, something went wrong!"));

                if ($this->config->exception & UUP_SITE_EXCEPT_LOG) {
                        error_log(print_r($this->_exception, true));
                }
                if ($this->config->exception & UUP_SITE_EXCEPT_SILENT) {
                        printf(_("An exception occured, but error reporing has been suppressed by the system manager."));
                        return;
                }
                if ($this->config->exception & UUP_SITE_EXCEPT_BRIEF) {
                        printf("<b>%s:</b> %s<br/>\n", get_class($this->_exception), $this->_exception->getMessage());
                }
                if ($this->config->exception & UUP_SITE_EXCEPT_CODE) {
                        printf("<b>Code:</b> %s<br/>\n", $this->_exception->getCode());
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

        /**
         * Display exception on error page.
         * @param Exception $exception The exception object.
         */
        public final static function show($exception)
        {
                $page = new ErrorPage($exception);
                $page->render();
        }

}
