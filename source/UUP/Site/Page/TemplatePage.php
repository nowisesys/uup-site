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

namespace UUP\Site\Page;

use UUP\Site\Page\Context\Headers;
use UUP\Site\Page\Context\Menus;
use UUP\Site\Page\Context\Publisher;
use UUP\Site\Utility\Config;

/**
 * Interface for page classes.
 * 
 * @author Anders Lövgren (QNET/BMC CompDept)
 * @package UUP
 * @subpackage Site
 */
interface TemplatePage
{

        /**
         * Print extra header except for theme default.
         */
        function printHeader();

        /**
         * Print main body content.
         */
        function printContent();

        /**
         * Get page title.
         * @return string
         */
        function getTitle();
        
        /**
         * Get page menus.
         * @return Menus
         */
        function getMenus();

        /**
         * Get publish info.
         * @return Publisher 
         */
        function getPublisher();

        /**
         * Get site configuration.
         * @return Config 
         */
        function getConfig();
        
        /**
         * Get custom HTTP headers.
         * @return Headers 
         */
        function getHeaders();
}
