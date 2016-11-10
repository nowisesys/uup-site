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

namespace UUP\Site\Page\Context;

/**
 * Page publisher information.
 * 
 * @property-read array $contact The contact info for page responsible (href/name).
 * @property-read array $editor  The webmaster/editor info (href/name).
 * @property-read string $copying The creation/copyright year.
 * @property-read string $updated The last modification time.
 * 
 * @author Anders Lövgren (QNET/BMC CompDept)
 * @package UUP
 * @subpackage Site
 */
class Publisher extends \ArrayObject
{

        /**
         * Constructor.
         * @param string $template The template directory.
         * @param array|boolean $publish Publish information.
         * @return type
         */
        public function __construct($template, $publish = false)
        {
                if (file_exists("publish.inc")) {
                        parent::__construct(include("publish.inc"));
                } elseif (file_exists(sprintf("%s/publish.inc", $template))) {
                        parent::__construct(include(sprintf("%s/publish.inc", $template)));
                } elseif (is_array($publish)) {
                        parent::__construct($publish);
                } else {
                        parent::__construct(array(
                                "copying" => date('Y'),
                                "updated" => getlastmod()
                        ));
                }
        }

        public function __get($name)
        {
                if (isset($this[$name])) {
                        return $this[$name];
                }
        }

}
