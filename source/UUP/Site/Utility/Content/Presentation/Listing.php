<?php

/*
 * Copyright (C) 2018 Anders Lövgren (Nowise Systems/BMC-IT, Uppsala University).
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

namespace UUP\Site\Utility\Content\Presentation;

use UUP\Web\Component\Container\Gallery\Presentation;
use UUP\Web\Component\Container\Gallery\Presentation\Expose;
use UUP\Web\Component\Container\Gallery\Scanner\ContentSpecScanner;

/**
 * Render table of content (TOC).
 * 
 * This is a convenience class around the presentation and scanner classes
 * from the web components package. It's a specialization for content.spec
 * files for rendering a table of content.
 *
 * The default is to render current directory:
 * <code>
 * $listing = new ContentListing();
 * $listing->render();
 * </code>
 * 
 * It's also possible to render multiple sub directories:
 * <code>
 * $listing = new ContentListing();
 * $listing->render("proj");
 * $listing->render("docs");
 * </code>
 * 
 * Multiple pathes can be combined in the same presentation by calling
 * scan on multiple locations. In this case, the boolean true must be
 * passed to the render() method:
 * <code>
 * $listing = new ContentListing();
 * $listing->scan("proj");
 * $listing->scan("docs");
 * $listing->render(true);
 * </code>
 * 
 * If previous collected content should
 * @author Anders Lövgren (Computing Department at BMC, Uppsala University)
 */
class Listing
{

        /**
         * The presentation gallery.
         * @var Presentation 
         */
        private $_gallery;
        /**
         * The render type (cell, card or grid).
         * @var string 
         */
        public $type = 'cell';
        /**
         * Use flat layout.
         * @var boolean 
         */
        public $flat = false;

        /**
         * Constructor.
         * @param Presentation $gallery The presentation gallery.
         */
        public function __construct($gallery = null)
        {
                $this->_gallery = $gallery;
        }

        public function reset()
        {
                $this->_gallery = new get_class($this->_gallery);
        }

        public function scan($path)
        {
                $gallery = $this->getGallery();
                $gallery->flat = $this->flat;

                $scanner = new ContentSpecScanner($gallery);
                $scanner->itemtype = $this->type;
                $scanner->find($path);
        }

        /**
         * Scan and render path.
         * 
         * @param string $path The path to scan.
         */
        public function render($path = ".")
        {
                $gallery = $this->getGallery();

                if ($gallery->hasComponents()) {
                        $this->reset();
                }
                if (is_string($path)) {
                        $this->scan($path);
                }
                if ($gallery->hasComponents()) {
                        $gallery->render();
                }
        }

        /**
         * Get presentation gallery.
         * 
         * @return Expose
         */
        private function getGallery()
        {
                if (!isset($this->_gallery)) {
                        return $this->_gallery = new Expose();
                } else {
                        return $this->_gallery;
                }
        }

}
