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

use RuntimeException;

/**
 * Find content spec files in sub directories.
 *
 * @author Anders Lövgren (Computing Department at BMC, Uppsala University)
 */
class Explore
{

        /**
         * The start directory.
         * @var string 
         */
        private $_topdir;
        /**
         * The explored files.
         * @var array 
         */
        private $_files;

        /**
         * Constructor. 
         * 
         * @param string $dir The start directory.
         * @param boolean $sort Sort array on names (from content spec).
         * @throws RuntimeException
         */
        public function __construct($dir = ".", $sort = 'name')
        {
                $this->_topdir = $dir;
                $this->scanContent($dir, $sort);
        }

        /**
         * Get all files.
         * @return array
         */
        public function getFiles()
        {
                return $this->_files;
        }

        /**
         * Get start directory.
         * @return string
         */
        public function getStart()
        {
                return $this->_topdir;
        }

        /**
         * Get data relocated to start directory.
         * @param array $data
         */
        public function getRelocated($data)
        {
                $data['image'] = sprintf(
                    "%s/%s", $this->_topdir, $data['images']
                );
                return $data;
        }

        /**
         * Scan an additional directory.
         * 
         * This method scans another directory for content spec files and
         * adds them to the list of already scanned files. Normally only a
         * single directory should be scanned, and that is done from the
         * constructor.
         * 
         * @param string $dir The start directory.
         * @param boolean $sort Sort array on names (from content spec).
         * @throws RuntimeException
         */
        public function scanContent($dir = ".", $sort = 'name')
        {
                if (!($handle = popen("find $dir -name content.spec", "r"))) {
                        throw new RuntimeException("Failed open pipe ($dir)");
                }

                while (($file = trim(fgets($handle)))) {
                        $this->_files[$file] = require($file);
                }

                if (pclose($handle) < 0) {
                        throw new RuntimeException("Failed close pipe");
                }

                if (isset($sort)) {
                        uasort($this->_files, function($a, $b) use($sort) {
                                return strcasecmp($a[$sort], $b[$sort]);
                        });
                }
        }

}
