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
 * Image gallery component.
 * 
 * @property-read array $images The array of images.
 *
 * @author Anders Lövgren (Computing Department at BMC, Uppsala University)
 * @package UUP
 * @subpackage Site
 */
class ImageGallery
{

        private $_images;

        public function __construct($images = array())
        {
                $this->_images = $images;
        }

        public function __get($name)
        {
                switch ($name) {
                        case 'images':
                                return $this->_images;
                }
        }

        public function scan($images, $thumbs = null)
        {
                
        }

}
