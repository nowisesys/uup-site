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

require_once(realpath(__DIR__ . '/../../../../vendor/autoload.php'));

use UUP\Site\Page\StandardPage;

class IndexPage extends StandardPage
{

        public function __construct()
        {
                parent::__construct(__CLASS__);
                $this->config->publish = array(
                        "contact" => array(
                                "href" => "http://directory.example.com/info/?id=4775",
                                "name" => _("Page Responsible")
                        ),
                        "editor"  => array(
                                "href" => "http://www.example.com/webmaster",
                                "name" => _("Webmaster")
                        ),
                        "copying" => sprintf("2015-%s", date('Y')),
                        "updated" => getlastmod()
                );
        }

        public function printContent()
        {
                echo "<h1>Publisher information</h1>\n";
                echo "<p>Example on displaying page publisher information defined by the class. This is the object oriented approach at handling publisher information.</p>\n";

                echo "<p><pre><code>\n";
                print_r($this->publisher);
                echo "</code></pre></p>\n";
        }

}

$page = new IndexPage();
$page->render();
