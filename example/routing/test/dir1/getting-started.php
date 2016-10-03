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

use UUP\Site\Page\StandardPage;

class GettingStartedPage extends StandardPage
{

        public function __construct()
        {
                parent::__construct("Wanting to get started?");
        }

        public function printContent()
        {
                echo "<h1>You have already getting started again!</h1>\n";
                echo "<hr/>\n";
                echo "<p>If you see this message, then routing is working as expected.</p>\n";
        }

}
