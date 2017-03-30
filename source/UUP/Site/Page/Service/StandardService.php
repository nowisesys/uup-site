<?php

/*
 * Copyright (C) 2017 Anders Lövgren (QNET/BMC CompDept).
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

namespace UUP\Site\Page\Service;

use UUP\Site\Request\Handler as RequestHandler;

/**
 * Standard web service.
 *
 * @author Anders Lövgren (QNET/BMC CompDept)
 * @package UUP
 * @subpackage Site
 */
abstract class StandardService extends RequestHandler
{

        /**
         * Constructor.
         * @param string $config The defaults.site configuration file.
         */
        public function __construct($config = null)
        {
                if (ob_get_level() == 0) {
                        ob_start();
                }

                parent::__construct($config);
        }

}
