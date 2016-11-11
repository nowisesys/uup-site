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

namespace UUP\Site\Page;

/**
 * Secure page enforcing authentication.
 *
 * @author Anders Lövgren (QNET/BMC CompDept)
 * @package UUP
 * @subpackage Site
 */
class SecurePage extends StandardPage
{

        /**
         * The authenti
         * @var type 
         */
        private $_auth;

        /**
         * Constructor.
         * @param string $title The page title.
         * @param string $template The output formatting.
         * @param string $config The defaults.site configuration file.
         */
        public function __construct($title, $template = "standard", $config = null)
        {
                parent::__construct($title, $template, $config);
        }

        public function printContent()
        {
                
        }

}
