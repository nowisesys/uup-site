<?php

use UUP\Site\Page\StandardPage;

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

/**
 * Output HTML elements.
 * 
 * This is a helper class for the default theme. It's sort of a demonstration of
 * how to escape from the pain of mixing PHP code inline in HTML, but also shows
 * that we can take advantage of the IDE auto complete.
 */
class format
{

        /**
         * @var StandardPage
         */
        private $page;
        private $hash;

        /**
         * Constructor.
         * @param StandardPage $page The page to render.
         */
        public function __construct($page)
        {
                $this->page = $page;
                $this->hash = md5(basename(filter_input(INPUT_SERVER, 'REQUEST_URI')));
        }

        public function hash()
        {
                return $this->hash;
        }

        public function topmenu()
        {
                foreach ($this->page->topmenu as $name => $link) {
                        printf("<li id=\"%s\" class=\"topmenu\"><a href=\"%s\">%s</a></li>\n", md5(basename($link)), $link, $name);
                }
        }

        public function navmenu()
        {
                foreach ($this->page->navmenu as $menu) {
                        foreach ($menu as $name => $link) {
                                echo "<li><a href=\"$link\">$name</a></li>\n";
                        }
                }
        }

        public function sidemenu()
        {
                foreach ($this->page->sidemenu as $hval => $menu) {
                        echo "<div class=\"well\">\n";
                        foreach ($menu as $name => $link) {
                                echo "<li><a href=\"$link\">$name</a></li>\n";
                        }
                        echo "</div>\n";
                }
        }

        public function sections()
        {
                foreach ($this->page->sections as $head => $cont) {
                        printf("<div class=\"col-sm-4 section\">\n");
                        printf("<div class=\"secthead\">%s</div>\n", $head);
                        printf("<img src=\"%s\"/><br/>\n", $cont['image']);
                        printf("<div class=\"secttext\">%s", $cont['text']);
                        printf("<button type=\"button\" class=\"btn btn-default\" onclick=\"location.href = '%s'\">More</button>\n", $cont['link']);
                        printf("</div>\n</div>\n");
                }
        }

        public function footer()
        {
                $publisher = $this->page->publisher;

                if ($publisher->count()) {
                        if ($publisher->updated) {
                                printf("<div class=\"updated\"><small>Modified: %s</small></div>\n", strftime("%x %X", $publisher->updated));
                        }
                        if ($publisher->copying) {
                                printf("<div class=\"copying\"><small>&copy %s</small></div>\n", $publisher->copying);
                        }
                }
        }

}