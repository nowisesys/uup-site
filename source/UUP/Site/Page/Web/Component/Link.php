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

namespace UUP\Site\Page\Web\Component;

/**
 * HTML link component.
 * 
 * @property string $name The link text.
 * @property array $attr The link attributes.
 * 
 * @author Anders Lövgren (QNET/BMC CompDept)
 * @package UUP
 * @subpackage Site
 */
class Link
{

        /**
         * Constructor.
         * @property string $name The link text.
         * @property array $attr Optional link attributes.
         */
        public function __construct($name = null, $attr = null)
        {
                if (is_array($attr)) {
                        $this->name = $name;
                        $this->attr = $attr;
                } elseif ($attr[0] == ':') {
                        $this->name = $name;
                        $this->attr = self::parse($attr);
                } else {
                        $this->name = $name;
                        $this->attr = array('href' => $attr);
                }
                if (!isset($this->attr['href'])) {
                        $this->attr['href'] = '#';
                }
                if (!array_key_exists('id', $this->attr)) {
                        $this->attr['id'] = md5(basename($this->attr['href']));
                }
        }

        public function render()
        {
                $attr = array();

                foreach ($this->attr as $key => $val) {
                        $attr[] = sprintf("%s=\"%s\"", $key, $val);
                }

                printf("<a %s>%s</a>\n", implode(" ", $attr), $this->name);
        }

        private static function parse($href)
        {
                if (preg_match('/^:(.*?):(.*)$/', $href, $match)) {
                        if (empty($match[1])) {
                                return array(
                                        'href'    => '#',
                                        'onclick' => sprintf("content_replace(event, 'page-content', '%s')", $match[2])
                                );
                        } else {
                                return array(
                                        'href'    => '#',
                                        'onclick' => sprintf("content_replace(event, '%s', '%s')", $match[1], $match[2])
                                );
                        }
                }
        }

}
