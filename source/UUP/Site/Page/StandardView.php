<?php

/*
 * Copyright (C) 2016 Anders Lövgren (Computing Department at BMC, Uppsala University).
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

namespace UUP\Site\Page;

use UUP\Site\Page\StandardPage;

/**
 * Simple view support.
 * 
 * This class supports rendering of HTML fragments (called the view) inside 
 * a standard page. Multiple views may be passed to constructor and each view 
 * gets rendered inside the template body as its content.
 * 
 * The base class inheritance makes it possible to use the ordinary context
 * files (sidebar/topbar menus and publisher).
 * 
 * See example/view for demo.
 * 
 * @author Anders Lövgren (QNET/BMC CompDept)
 * @package UUP
 * @subpackage Site
 */
class StandardView extends StandardPage
{

        /**
         * The views to render.
         * @var array 
         */
        private $_views;

        /**
         * Constructor.
         * @param string $title The page title.
         * @param string|array $view The view to render (might be multiple rendered in sequence).
         */
        public function __construct($title, $view)
        {
                parent::__construct($title);

                if (is_array($view)) {
                        $this->_views = $view;
                } else {
                        $this->_views = array($view);
                }
        }

        public function printContent()
        {
                foreach ($this->_views as $view) {
                        include($view);
                }
        }

}
