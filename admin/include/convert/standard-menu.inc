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

class StandardMenu
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

                if (!isset($content['urls']) ||
                    !isset($content['name'])) {
                        throw new Exception("Required name and urls is missing");
                }

                $result = array(
                        'head' => $content['name'],
                        'data' => array()
                );

                foreach ($content['urls'] as $name => $data) {
                        $insert = array();

                        // 
                        // Collect data:
                        // 
                        if (is_string($name)) {
                                $insert['href'] = $name;
                        }
                        if (is_string($data)) {
                                $insert['name'] = $data;
                        }
                        if (isset($data['href'])) {
                                $insert['href'] = $data['href'];
                        }
                        if (isset($data['name'])) {
                                $insert['name'] = $data['name'];
                        }
                        if (isset($data['desc'])) {
                                $insert['desc'] = $data['desc'];
                        }

                        // 
                        // Set defaults and validate:
                        // 
                        if (!isset($insert['href'])) {
                                $insert['href'] = "#";
                        }
                        if (!isset($insert['desc'])) {
                                $insert['desc'] = "";
                        }
                        if (!isset($insert['name'])) {
                                throw new Exception("Missing required value for name");
                        }

                        // 
                        // Add formatted data:
                        // 
                        $result['data'][$insert['name']] = array(
                                'href'  => $insert['href'],
                                'title' => $insert['desc']
                        );
                }

                fprintf($out, "<?php\n");
                fprintf($out, "\n");
                fprintf($out, "return %s;\n", var_export($result, true));
        }

}
