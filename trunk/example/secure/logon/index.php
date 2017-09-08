<?php

/*
 * Copyright (C) 2017 Anders Lövgren (Computing Department at BMC, Uppsala University).
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

require_once(realpath(__DIR__ . '/../../../vendor/autoload.php'));

use UUP\Authentication\Exception as LogonException;
use UUP\Site\Page\Web\Security\LogonPage;

class IndexPage extends LogonPage
{

        public function __construct()
        {
                parent::__construct();

                if (!$this->config->tools['auth']) {
                        throw new LogonException(_("Logon has been disabled by system config"));
                }
        }

        public function printContent()
        {
                parent::printContent();

                if ($this->config->debug) {
                        echo "<p><pre><code>\n";
                        print_r($this->session);
                        echo "</code></pre></p>\n";
                }
        }

}

$page = new IndexPage();
$page->render();
