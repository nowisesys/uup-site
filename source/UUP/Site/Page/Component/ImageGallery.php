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
 * @property array $images The array of images.
 * @property int $height The height of thumbnails.
 * @property int $width The width of thumbnails.
 *
 * @author Anders Lövgren (Computing Department at BMC, Uppsala University)
 * @package UUP
 * @subpackage Site
 */
class ImageGallery implements Component
{

        private $_images;

        public function __construct($images = array())
        {
                $this->_images = $images;
        }

        /**
         * Scan an image directory.
         * 
         * The thumbs argument is either a string (e.g. "small") or a callback 
         * that receives the image path as argument and is expected to return
         * an thumbnail path. 
         * 
         * When callback is used, it can also return false to exclude an image
         * from the gallery.
         * 
         * The images directory path should be relative. The scan is performed
         * from inside that directory.
         * 
         * @param string $images The image directory path.
         * @param string|callable $thumbs The thumbnail URL generator.
         */
        public function scan($images, $thumbs = null)
        {
                if (!file_exists($images)) {
                        throw new \Exception("The image diretory $images is missing");
                }
        }

        /**
         * Render gallery as HTML.
         */
        public function render()
        {
                
        }

}
