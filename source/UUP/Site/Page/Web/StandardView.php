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

namespace UUP\Site\Page\Web;

use LogicException;
use RangeException;

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
 * @author Anders Lövgren (Nowise Systems/BMC-IT, Uppsala University)
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
                if (is_array($view)) {
                        $this->_views = $view;
                } else {
                        $this->_views = array($view);
                }

                parent::__construct($title);
        }

        public function printContent()
        {
                foreach ($this->_views as $view) {
                        include($view);
                }
        }

        public function isEditable()
        {
                return true;
        }

        protected function putContent($content)
        {
                try {
                        $index = 0;
                        $count = count($this->_views);

                        if ($count == 0) {
                                throw new LogicException("No view pages are defined");
                        }

                        if ($this->params->hasParam('index')) {
                                $index = $this->params->getParam('index');
                        }
                        if ($index >= $count || $index < 0) {
                                throw new RangeException("Expected index between 0 and $count");
                        } else {
                                $target = $this->_views[$index];
                        }

                        echo json_encode(array(
                                'status' => 'success',
                                'result' => $this->setContent($content, $target)
                        ));
                } catch (\Exception $exception) {
                        echo json_encode(array(
                                'status'  => 'failure',
                                'message' => $exception->getMessage(),
                                'code'    => $exception->getCode()
                        ));
                } finally {
                        exit(0);
                }
        }

}
