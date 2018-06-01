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

namespace UUP\Site\Page\Web;

use UUP\Site\Page\Context\Content;
use UUP\Site\Page\Context\Menus;
use UUP\Site\Page\Context\Publisher;

/**
 * Interface for page classes.
 * 
 * @author Anders Lövgren (Nowise Systems/BMC-IT, Uppsala University)
 * @package UUP
 * @subpackage Site
 */
interface PageTemplate
{

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
         * Get custom content specification.
         * @return Content
         */
        function getContent();
        
        /**
         * Print main body content.
         */
        function printContent();
}
