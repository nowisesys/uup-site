<?php

/*
 * Copyright (C) 2017 Anders Lövgren (Nowise Systems/BMC-IT, Uppsala University).
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

use DomainException;
use UUP\Authentication\Exception as LogonException;

/**
 * Secured web service.
 *
 * @author Anders Lövgren (Nowise Systems/BMC-IT, Uppsala University)
 * @package UUP
 * @subpackage Site
 */
abstract class SecureService extends StandardService
{

        public function __construct($config = null)
        {
                parent::__construct($config);

                if (!$this->config->auth) {
                        throw new DomainException(_("Authentication are disabled"));
                }
                if (!$this->validate()) {
                        throw new LogonException(_("Authentication is required"));
                }
        }

}
