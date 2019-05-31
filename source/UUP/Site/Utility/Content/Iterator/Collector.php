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

namespace UUP\Site\Utility\Content\Iterator;

/**
 * Directory content collector.
 *
 * @author Anders Lövgren (Nowise Systems/BMC-IT, Uppsala University)
 */
class Collector
{

        /**
         * The file system iterator.
         * @var FilesystemIterator 
         */
        private $_iterator;
        /**
         * MIME database handle.
         * @var resource 
         */
        private $_fileinfo;

        /**
         * Constructor.
         * @param FilesystemIterator $iterator The file system iterator.
         */
        public function __construct($iterator)
        {
                $this->_iterator = $iterator;
        }

        /**
         * Destructor.
         */
        public function __destruct()
        {
                if (isset($this->_fileinfo)) {
                        finfo_close($this->_fileinfo);
                }
        }

        /**
         * Check if MIME type is supported.
         * @return bool
         */
        public function hasFileType()
        {
                return extension_loaded('fileinfo');
        }

        /**
         * Set MIME type support.
         * @param bool $enable Enable or disable MIME type support.
         */
        public function useFileType($enable = true)
        {
                if (extension_loaded('fileinfo')) {
                        if ($enable) {
                                $this->_fileinfo = finfo_open();
                        } elseif (isset($this->_fileinfo)) {
                                finfo_close($this->_fileinfo);
                        }
                }
        }

        /**
         * Get file info.
         * 
         * @param bool $sorted Sort files and directories by name.
         * @return array
         */
        public function getContent($sorted = true)
        {
                $result = array(
                        'dir'  => array(),
                        'file' => array()
                );

                foreach ($this->_iterator as $fileinfo) {
                        if ($fileinfo->isDir()) {
                                $result['dir'][] = $this->getEntry($fileinfo);
                        } else {
                                $result['file'][] = $this->getEntry($fileinfo);
                        }
                }

                if ($sorted) {
                        usort($result['dir'], function($a, $b) {
                                return strcmp($a['name'], $b['name']);
                        });
                        usort($result['file'], function($a, $b) {
                                return strcmp($a['name'], $b['name']);
                        });
                }

                return $result;
        }

        /**
         * Get file info entry.
         * @param \SplFileInfo $fileinfo The file info object.
         * @return array
         */
        private function getEntry($fileinfo)
        {
                $entry = array(
                        'name'  => $fileinfo->getFilename(),
                        'link'  => null,
                        'mime'  => null,
                        'owner' => posix_getpwuid($fileinfo->getOwner())['name'],
                        'size'  => 0
                );

                if ($fileinfo->isLink()) {
                        $entry['link'] = $fileinfo->getLinkTarget();
                }
                if ($fileinfo->isFile()) {
                        $entry['size'] = $fileinfo->getSize();
                }

                if (isset($this->_fileinfo)) {
                        $entry['mime'] = finfo_file($this->_fileinfo, $fileinfo->getRealPath(), FILEINFO_MIME_TYPE);
                        $entry['type'] = finfo_file($this->_fileinfo, $fileinfo->getRealPath(), FILEINFO_NONE);
                }

                return $entry;
        }

}
