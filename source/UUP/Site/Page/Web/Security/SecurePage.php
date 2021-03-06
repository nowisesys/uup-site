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

namespace UUP\Site\Page\Web\Security;

use DomainException;
use UUP\Site\Page\Web\StandardPage;

/**
 * Secure page enforcing authentication.
 * 
 * @author Anders Lövgren (Nowise Systems/BMC-IT, Uppsala University)
 * @package UUP
 * @subpackage Site
 */
abstract class SecurePage extends StandardPage
{

        /**
         * Constructor.
         * @param string $title The page title.
         * @param string $template The output formatting.
         * @param string $config The defaults.site configuration file.
         */
        public function __construct($title, $template = "standard", $config = null)
        {
                parent::__construct($title, $template, $config);

                if (!$this->config->auth) {
                        throw new DomainException(_("Authentication are disabled"));
                }
                if (!$this->validate()) {
                        $this->redirect($this->config->auth['logon']);
                }
        }

        public static function printTitle($header)
        {
                printf("<h1>$header<i class=\"fas fa-shield-alt\" style=\"color: #e6e6ff; float: right; margin-top: 10px\"></i></h1>\n");
        }

}
