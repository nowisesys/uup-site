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
 * @property-read string $parent The parent link.
 * 
 * @author Anders Lövgren (Computing Department at BMC, Uppsala University)
 */
class MenuData
{

        /**
         * Constructor.
         * @param array $data The header data.
         * @param string $parent The parent directory.
         */
        public function __construct($data, $parent = null)
        {
                $this->parent = $parent;

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

        /**
         * Relocate link targets in menu.
         * @param array $data The header data.
         * @param string $parent The parent directory.
         */
        private static function relocate(&$data, $parent)
        {
                if (isset($data['data'])) {
                        foreach ($data['data'] as $name => $link) {
                                if (!is_array($link)) {
                                        $href = $link;
                                } elseif (!isset($link['href'])) {
                                        $href = "#";
                                } else {
                                        $href = $link['href'];
                                }

                                if (!is_string($href)) {
                                        continue;       // Skip separator                                        
                                } elseif ($href[0] == '<') {
                                        continue;       // Skip separator 
                                } elseif ($href[0] == '@') {
                                        continue;       // Skip site anchor
                                } elseif (preg_match('%http(s)?://.*%', $href)) {
                                        continue;       // Skip external link
                                } elseif ($href[0] == ':') {
                                        if (preg_match('%^:(.*?):(.*)%', $href, $parts)) {
                                                $href = sprintf(":%s:%s/%s", $parts[1], $parent, $parts[2]);
                                        }
                                } else {
                                        $href = $parent . '/' . $href;
                                }

                                if (is_string($link)) {
                                        $data['data'][$name] = $href;
                                } else {
                                        $data['data'][$name]['href'] = $href;
                                }
                        }
                }
        }

}
