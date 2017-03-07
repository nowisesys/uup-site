<?php

/*
 * Copyright (C) 2016-2017 Anders Lövgren (Computing Department at BMC, Uppsala University).
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

class IndexPage extends StandardPage
{
        
        public function __construct()
        {
                parent::__construct(__CLASS__);
        }

        public function printContent()
        {
                echo "<h1>Secure page test</h1>\n";
                
                echo "<p>Authentication support has to be enabled in the config for this examples to work. \n";
                echo "The logon status is keept in session (under auth). When accessing the secured page, ";
                echo "authentication will be enforced if not yet logged on.</p>\n";
                
                echo "<p>When being on an ordinary page, logon can be triggered by clicking on the open keylock in the menu. \n";
                echo "When logged in, the keylock should be shown as closed and clicking on it should display a popup for logging out.</p>\n";
        }

}

$page = new IndexPage();
$page->render();
