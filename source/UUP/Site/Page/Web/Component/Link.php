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

namespace UUP\Site\Page\Web\Component;

/**
 * HTML link component.
 * 
 * This class helps rendering a link or menu separator. It's typical used by a 
 * theme to render navmenu, topmenu or sidemenu arrays.
 * 
 * @property string $name The link name.
 * @property string $href The link target.
 * @property array $attr Optional link attributes.
 * 
 * @author Anders Lövgren (Nowise Systems/BMC-IT, Uppsala University)
 * @package UUP
 * @subpackage Site
 */
class Link
{

        /**
         * Default HTML for separator.
         */
        const SEPARATOR = '<hr class="menu-separator" style="margin: 8px 16px">';

        /**
         * Constructor.
         * @param string $name The link text.
         * @param string|array $attr The link attributes.
         */
        public function __construct($name = null, $attr = null)
        {
                if (is_numeric($name) && $attr === false) {
                        $this->name = self::SEPARATOR;  // Separator
                        $this->href = false;
                        $this->attr = array();
                } elseif (is_numeric($name) && is_array($attr)) {
                        $this->name = null;             // Separator
                        $this->href = false;
                        $this->attr = $attr;
                } elseif (is_numeric($name)) {
                        $this->name = $attr;            // Separator
                        $this->href = false;
                        $this->attr = array();
                } elseif ($attr === false) {
                        $this->name = self::SEPARATOR;  // Separator
                        $this->href = false;
                        $this->attr = array();
                } elseif (!isset($attr) || is_string($attr)) {
                        $this->name = $name;            // Link ['name' => 'link']
                        $this->href = $attr;
                        $this->attr = array();
                } elseif (isset($attr['href'])) {
                        $this->name = $name;            // Link ['name' => array('href' => 'link)]
                        $this->href = $attr['href'];
                        $this->attr = $attr;
                } else {
                        $this->name = $name;            // Link ['name' => array(...)]
                        $this->href = '#';
                        $this->attr = $attr;
                }

                if (isset($this->attr['href'])) {
                        unset($this->attr['href']);
                }

                if (!isset($this->href)) {
                        $this->href = '#';
                }
                if (!isset($this->attr)) {
                        $this->attr = array();
                }
                if (!isset($this->name)) {
                        $this->name = "";
                }
        }

        /**
         * Render this link.
         * @param type $location
         */
        public function render($location)
        {
                $this->prepare($location);
                $this->output();
        }

        private function output()
        {
                $attr = array();

                foreach ($this->attr as $key => $val) {
                        $attr[] = sprintf("%s=\"%s\"", $key, $val);
                }
                if ($this->href) {
                        printf("<a %s>%s</a>\n", implode(" ", $attr), $this->name);
                } elseif (count($attr)) {
                        printf("<hr %s>\n", implode(" ", $attr));
                } else {
                        printf("%s\n", $this->name);
                }
        }

        private function prepare($location)
        {
                // 
                // Early return if separator:
                // 
                if ($this->href === false) {
                        return;
                }

                // 
                // Set dummy target:
                // 
                if (!isset($this->href)) {
                        $this->href = '#';
                }

                // 
                // Relocate link relative to site root:
                // 
                if (strpos($this->href, '@') !== false) {
                        $this->href = self::relocate($this->href, $location);
                }

                // 
                // Prefix link with dest attribute if defined:
                // 
                if (isset($this->attr['dest'])) {
                        $this->href = sprintf(":%s:%s", $this->attr['dest'], $this->href);
                        unset($this->attr['dest']);
                }

                // 
                // Parse dynamic content links:
                // 
                if ($this->href[0] == ':') {
                        $this->attr = array_merge($this->attr, self::parse($this->href, $location));
                } else {
                        $this->attr = array_merge($this->attr, array('href' => $this->href));
                }
        }

        private static function parse($href, $location)
        {
                $match = array();

                if (preg_match('/^:(.*?):(.*)$/', $href, $match)) {
                        if (isset($match[2])) {
                                $match[2] = self::relocate($match[2], $location);
                        }
                        if (empty($match[1])) {
                                $match[1] = 'page-content';
                        }
                        return array(
                                'href'    => $match[2],
                                'onclick' => sprintf("content_replace(event, '%s', '%s')", $match[1], $match[2])
                        );
                }
        }

        private static function relocate($href, $location)
        {
                if (strpos($href, '@') === false) {
                        return $href;
                } else {
                        return str_replace('@', $location, $href);
                }
        }

}
