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

require_once(realpath(__DIR__ . '/../../vendor/autoload.php'));

use UUP\Site\Page\Web\StandardPage;
use UUP\Site\Utility\Content\Runtime;

class RuntimePage extends StandardPage
{

        public function __construct()
        {
                parent::__construct(__CLASS__);
        }

        public function printContent()
        {
                echo "<h1>Runtime options</h1>\n";
                echo "<p>The Runtime class helps themes to decide which UI part should be displayed. ";
                echo "It collects options from system config and uses runtime qualities (like if ";
                echo "user is authenticated) to determine properties.</p>\n";
                
                $runtime = new Runtime($this);
                echo "<code><pre>\n";
                print_r($runtime);
                echo "</pre></code>\n";
        }

}

$page = new RuntimePage();
$page->render();
