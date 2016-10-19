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
 * Theme specific formatting.
 * 
 * An theme can derive from this class to influence how some GUI components 
 * are rendered. Notice that while the theme can define the look & feel in
 * general (i.e. menus and footer), it has limited control over the actual 
 * content.
 * 
 * This class tries to solve that problem by allowing themes to redefine how
 * section, cards and galleries are output. Each page gets an handle to the
 * theme formatter passed and can use it when rendering content.
 * 
 * <code>
 * class MyPage extends StandardPage
 * {
 *      public function printContent() 
 *      {
 *              $gallery = new ImageGallery();
 *              $gallery->scan("images", "small");
 * 
 *              $this->formatter->outputGallery($gallery);
 *      }
 * }
 * </code>
 * 
 * @author Anders Lövgren (Computing Department at BMC, Uppsala University)
 * @package UUP
 * @subpackage Site
 */
class Formatter
{

        public function outputGallery($gallery)
        {
                
        }

        public function outputCard($card)
        {
                
        }

        public function outputSection($section)
        {
                
        }

}
