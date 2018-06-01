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

require_once(realpath(__DIR__ . '/../../../../vendor/autoload.php'));

use UUP\Site\Page\Web\StandardPage;

class IndexPage extends StandardPage
{

        public function __construct()
        {
                parent::__construct(__CLASS__);
                $this->config->headers = array(
                        'meta' => array(
                                array(
                                        'name'    => 'description',
                                        'content' => 'Site and application template system'
                                ),
                                array(
                                        'name'    => 'author',
                                        'content' => 'Anders Lövgren'
                                ),
                                array(
                                        'name'    => 'keywords',
                                        'content' => 'php,template,router'
                                ),
                                array(
                                        'http-equiv' => 'Content-Type',
                                        'content'    => 'text/html;charset=utf-8'
                                )
                        )
                );
        }

        public function printContent()
        {
                echo "<h1>Custom HTML headers</h1>\n";
                echo "<p>This example demonstrate output of custom HTML headers using an object oriented approach. We simply pass '\$this->config->headers = array(...)' during object initialization.</p>\n";
                echo "<p>Looking at the generated HTML should show &lt;meta name=\"...\" content=\"...\"&gt; and http-equiv tags rendered from header data.</p>\n";

                echo "<p><pre><code>\n";
                print_r($this->headers);
                echo "</code></pre></p>\n";
        }

}

$page = new IndexPage();
$page->render();
