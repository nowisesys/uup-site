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

require_once(realpath(__DIR__ . '/../../vendor/autoload.php'));

use UUP\Site\Page\Component\Button;
use UUP\Site\Page\StandardPage;

class ButtonsPage extends StandardPage
{
        public function __construct()
        {
                parent::__construct("Buttons");
        }

        public function printContent()
        {
                printf(__METHOD__);
                $this->formatter->printButton(new Button());
        }

}

$page = new ButtonsPage();
$page->render();
