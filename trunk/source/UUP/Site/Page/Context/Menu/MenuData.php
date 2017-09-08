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

namespace UUP\Site\Page\Context\Menu;

/**
 * Menu data.
 * 
 * @property-read string $name The header name.
 * @property-read array $data The header data.
 * 
 * @author Anders Lövgren (Computing Department at BMC, Uppsala University)
 */
class MenuData
{

        /**
         * Constructor.
         * @param array $data The header data.
         */
        public function __construct($data, $parent = null)
        {
                if (isset($parent)) {
                        self::relocate($data, $parent);
                }
                if (isset($data['head'])) {
                        $this->name = $data['head'];
                }
                if (isset($data['data'])) {
                        $this->data = $data['data'];
                }
        }

        private static function relocate(&$data, $parent)
        {
                if (isset($data['data'])) {
                        foreach ($data['data'] as $name => $link) {
                                if ($link) {
                                        $data['data'][$name] = $parent . '/' . $link;
                                }
                        }
                }
        }

}
