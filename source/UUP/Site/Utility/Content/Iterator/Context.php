<?php

/*
 * Copyright (C) 2017 Anders Lövgren (Computing Department at BMC, Uppsala University).
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

use DirectoryIterator;
use FilesystemIterator;
use FilterIterator;

/**
 * Context iterator.
 *
 * @author Anders Lövgren (Nowise Systems/BMC-IT, Uppsala University)
 * @package UUP
 * @subpackage Site
 */
class Context extends FilterIterator
{

        /**
         * Constructor.
         * @param string $path
         */
        public function __construct($path)
        {
                parent::__construct(new FilesystemIterator($path));
        }

        /**
         * Check if current element is acceptable.
         * @return boolean
         */
        public function accept()
        {
                return $this->matched($this->getInnerIterator()->current());
        }

        /**
         * Check if current file is acceptable.
         * @param DirectoryIterator $file The current item.
         * @return boolean
         */
        private function matched($file)
        {
                $include = array('content.spec', 'headers.inc', 'publish.inc');

                if (!$file->isFile() && !$file->isLink()) {
                        return false;
                }
                if (!$file->isWritable() && !$file->isReadable()) {
                        return false;
                }
                if (!in_array($file->getFilename(), $include)) {
                        return false;
                }

                return true;
        }

}
