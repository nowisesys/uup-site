<?php

/*
 * Copyright (C) 2016-2018 Anders Lövgren (Computing Department at BMC, Uppsala University).
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

use UUP\Site\Page\Web\Migration\TransitionalPage;

/**
 * Page converter class.
 * 
 * @author Anders Lövgren (Computing Department at BMC, Uppsala University)
 */
class Page
{

        /**
         * The class name.
         * @var string 
         */
        private $_name;
        /**
         * The source page object.
         * @var TransitionalPage 
         */
        private $_page;
        /**
         * Generate code for autoload.
         * @var bool 
         */
        private $_autoload = false;
        /**
         * The input stream.
         * @var resource 
         */
        private $_inp;
        /**
         * The output stream.
         * @var resource 
         */
        private $_out;

        /**
         * Constructor.
         * 
         * @param string $source The source file path.
         */
        public function __construct($source)
        {
                $this->_name = $this->getName();
                $this->_page = new TransitionalPage($source);
        }

        public function __set($name, $value)
        {
                if ($name === 'autoload') {
                        $this->_autoload = (bool) $value;
                }
        }

        /**
         * Output converted page.
         * @param resource $inp The input stream.
         * @param resource $out The output stream.
         */
        public function write($inp, $out)
        {
                $this->_inp = $inp;
                $this->_out = $out;

                $this->outputStart();
                $this->outputClass();
                $this->outputRender();
        }

        private function outputStart()
        {
                // 
                // Read until first include:
                // 
                while (($line = fgets($this->_inp))) {
                        $line = trim($line);
                        if (preg_match('/^include.*/', $line)) {
                                fprintf($this->_out, "// %s\n", $line);
                                break;
                        } else {
                                fprintf($this->_out, "%s\n", $line);
                        }
                }

                // 
                // Output autoload statement:
                // 
                $this->outputAutoload();

                // 
                // Output use statement:
                // 
                fprintf($this->_out, "\n%s\n", "use UUP\Site\Page\Web\StandardPage;");
        }

        private function outputClass()
        {
                // 
                // Output class definition:
                // 
                fprintf($this->_out, "\nclass %s extends StandardPage\n{\t\n", $this->_name);

                $this->outputConstructor();
                $this->outputCallbacks();

                fprintf($this->_out, "\n}\n");
        }

        private function outputCallbacks()
        {
                while (($line = fgets($this->_inp))) {
                        $line = trim($line);
                        if (preg_match('/^function print_body.*/', $line)) {
                                $this->outputPrintContent();
                        } elseif (preg_match('/^function print_header.*/', $line)) {
                                $this->outputPrintHeader();
                        }
                }
        }

        private function outputPrintHeader()
        {
                fprintf($this->_out, "\t\n");
                fprintf($this->_out, "\tpublic function printHeader()\n");
                fprintf($this->_out, "\t{\n");

                while (($line = fgets($this->_inp))) {
                        $line = rtrim($line);
                        if (strlen($line) == 0) {
                                $line = ' ';
                        } elseif ($line[0] == '}') {
                                break;
                        } elseif ($line[0] == '{') {
                                continue;
                        } else {
                                fprintf($this->_out, "\t\t%s\n", $line);
                        }
                }

                fprintf($this->_out, "\t}\n");
                fprintf($this->_out, "\t\n");
        }

        private function outputConstructor()
        {
                fprintf($this->_out, "\t\n");
                fprintf($this->_out, "\tpublic function __construct()\n");
                fprintf($this->_out, "\t{\n");
                fprintf($this->_out, "\t\tparent::__construct(\"%s\");\n", $this->_page->title);
                fprintf($this->_out, "\t}\n");
                fprintf($this->_out, "\t\n");
        }

        private function outputPrintContent()
        {
                fprintf($this->_out, "\t\n");
                fprintf($this->_out, "\tpublic function printContent()\n");
                fprintf($this->_out, "\t{\n");

                while (($line = fgets($this->_inp))) {
                        $line = rtrim($line);
                        if (strlen($line) == 0) {
                                $line = ' ';
                        } elseif ($line[0] == '}') {
                                break;
                        } elseif ($line[0] == '{') {
                                continue;
                        } else {
                                fprintf($this->_out, "\t\t%s\n", $line);
                        }
                }

                fprintf($this->_out, "\t}\n");
                fprintf($this->_out, "\t\n");
        }

        private function outputAutoload()
        {
                if ($this->_autoload) {
                        fprintf($this->_out, "\n%s\n", "require_once('vendor/autoload.php');");
                }
        }

        private function outputRender()
        {
                if ($this->_autoload) {
                        fprintf($this->_out, "\n");
                        fprintf($this->_out, "\$page = new %s();\n", $this->_name);
                        fprintf($this->_out, "\$page->render();\n");
                }
        }

        /**
         * Get class name.
         * 
         * @param string $source The source file path.
         * @return string
         */
        private function getName($source)
        {
                $parts = explode('-', basename($source));
                $parts = array_map('ucfirst', $parts);
                return str_replace(".php", "Page", implode('', $parts));
        }

}
