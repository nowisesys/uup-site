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

namespace UUP\Site\Page\Context\Menu;

/**
 * Top menu support class.
 *
 * @author Anders Lövgren (Nowise Systems/BMC-IT, Uppsala University)
 * @package UUP
 * @subpackage Site
 */
class TopMenu extends \ArrayObject
{

        /**
         * Constructor.
         * @param array|bool $topmeny The top menu.
         */
        public function __construct($topmeny = false)
        {
                if ($topmeny != false) {
                        if (file_exists("topbar.menu")) {
                                parent::__construct(include("topbar.menu"));
                        } elseif (is_array ($topmeny)) {
                                parent::__construct($topmeny);
                        }
                }
        }

}
