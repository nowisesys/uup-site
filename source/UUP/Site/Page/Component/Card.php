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

namespace UUP\Site\Page\Component;

/**
 * Card component.
 * 
 * Cards are UI components that consist of an header, some content and optional
 * buttons at bottom. Theirs intended use is to "box" some HTML and make it stand
 * out. Cards can also be stacked horizontal in a grid.
 *
 * @property string $header The card header.
 * @property string|callable $content The card content.
 * @property Button $buttons The array of buttons.
 * 
 * @author Anders Lövgren (Computing Department at BMC, Uppsala University)
 * @package UUP
 * @subpackage Site
 */
class Card implements Component
{
//        /**
//         * The card header.
//         * @var string 
//         */
//        private $_header;
//        /**
//         * The card content.
//         * @var string|callable
//         */
//        private $_content;
//        private $_buttons = array();
        
        public function __construct()
        {
                $this->buttons = array();
        }

        public function render()
        {
                
        }

}
