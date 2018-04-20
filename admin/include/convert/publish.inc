<?php

/*
 * Copyright (C) 2018 Anders Lövgren (Computing Department at BMC, Uppsala University).
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

class Publish
{

        /**
         * The source file.
         * @var string 
         */
        private $_source;

        /**
         * Constructor.
         * 
         * @param string $source The source file path.
         */
        public function __construct($source)
        {
                $this->_source = $source;
        }

        /**
         * Output converted page.
         * 
         * @param resource $inp The input stream.
         * @param resource $out The output stream.
         */
        public function write($inp, $out)
        {
                include($this->_source);

                $content = get_publish_info();

                $result = array(
                        'contact' => $content['editor'],
                        'editor'  => $content['webmaster'],
                        'copying' => $content['published'],
                        'updated' => "getlastmod()"
                );

                fprintf($out, "<?php\n");
                fprintf($out, "\n");
                fprintf($out, "return %s;\n", var_export($result, true));
        }

}
