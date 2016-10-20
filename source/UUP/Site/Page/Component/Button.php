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
 * Button component.
 * 
 * The button component provides an abstract button with optional actions. Its
 * up to current theme to render HTML matching the properties defined by the
 * button object.
 * 
 * The default button will be black with white text when using default theme.
 * 
 * @property string $text The button text.
 * @property string $color The button color.
 * @property boolean|string $border The border color or enable.
 * @property boolean $floating Circular floating button.
 * @property int $size The button size.
 * @property boolean $ripple Ripple effect on click.
 * 
 * @property string|callable $target The target on click (defaults to '#').
 * 
 * @author Anders Lövgren (Computing Department at BMC, Uppsala University)
 * @package UUP
 * @subpackage Site
 */
class Button implements Component
{

        /**
         * Small size.
         */
        const SIZE_SMALL = 1;
        /**
         * Normal size.
         */
        const SIZE_MEDIUM = 2;
        /**
         * Large size.
         */
        const SIZE_LARGE = 3;
        /**
         * Huge size.
         */
        const SIZE_HUGE = 4;

        public function __construct($options = array(
                'text'     => 'Button',
                'floating' => false
        ))
        {
                foreach ($options as $key => $val) {
                        $this->$key = $val;
                }
        }

        public function render()
        {
                $classes = array();

                if ($this->floating) {
                        $classes[] = 'w3-btn-floating';
                } else {
                        $classes[] = 'w3-btn';
                }

                printf("<button class=\"%s\">%s</button>\n", implode($classes, ' '), $this->text);
        }

}
