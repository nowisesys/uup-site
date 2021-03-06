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

namespace UUP\Site\Page\Context;

/**
 * Support for custom HTTP headers.
 *
 * @author Anders Lövgren (Nowise Systems/BMC-IT, Uppsala University)
 * @package UUP
 * @subpackage Site
 */
class Headers extends \ArrayObject
{

        /**
         * Constructor.
         * @param array|bool $headers Optional headers.
         */
        public function __construct($headers = false)
        {
                if ($headers != false) {
                        if (is_array($headers)) {
                                parent::__construct($headers);
                        } elseif (file_exists("headers.inc")) {
                                parent::__construct(include("headers.inc"));
                        }
                }
        }

}
