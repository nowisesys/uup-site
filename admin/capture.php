<?php

/*
 * Copyright (C) 2018 Anders Lövgren (Nowise Systems).
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

/**
 * HTML capture class.
 */
class Capture
{

        /**
         * The base URL.
         * @var string 
         */
        private $_base;

        /**
         * Constructor.
         * @param string $base The base URL.
         */
        public function __construct($base)
        {
                $this->_base = $base;
        }

        public function save($path, $force)
        {
                $content = $this->fetch($this->_base, $path);

                if ($force == false && file_exists("$path.inc")) {
                        throw new Exception("Refuse overwrite existing file $path.inc");
                }
                if (!file_put_contents("$path.inc", $content)) {
                        throw new Exception("Failed save $path.inc");
                }
        }

        public function read($path)
        {
                $content = $this->fetch($this->_base, $path);
                echo "$content\n";
        }

        public function test($path)
        {
                $content = $this->fetch($this->_base, $path);
                printf("[%d]:\t%s\n", strlen($content), $path);
        }

        private function fetch($base, $path)
        {
                if (!ini_get('allow_url_fopen')) {
                        throw new Exception("Using allow_url_fopen is not allowed");
                }

                if (($content = file_get_contents("$base/$path?theme=capture")) === false) {
                        throw new Exception("Failed read $base -> $path");
                }

                return $content;
        }

}

/**
 * The capture application.
 */
class Application
{

        /**
         * Read path and output mode.
         */
        const MODE_READ = 'read';
        /**
         * Read path and save to file (*.inc).
         */
        const MODE_SAVE = 'save';
        /**
         * Read path and report only.
         */
        const MODE_STAT = 'stat';

        /**
         * The capture object.
         * @var Capture 
         */
        private $_capture;
        /**
         * Save files.
         * @var boolean 
         */
        private $_mode = self::MODE_READ;
        /**
         * The pathes to capture.
         * @var array 
         */
        private $_pathes = array();
        /**
         * Force overwrite existing files.
         * @var boolean 
         */
        private $_force = false;

        /**
         * Process all pathes.
         */
        public function process()
        {
                foreach ($this->_pathes as $path) {
                        try {
                                switch ($this->_mode) {
                                        case self::MODE_READ:
                                                $this->_capture->read($path);
                                                break;
                                        case self::MODE_STAT:
                                                $this->_capture->test($path);
                                                break;
                                        case self::MODE_SAVE:
                                                $this->_capture->save($path, $this->_force);
                                                break;
                                }
                        } catch (Exception $exception) {
                                fprintf(STDERR, "(-) %s\n", $exception->getMessage());
                        }
                }
        }

        /**
         * Parse command line options.
         * @param int $argc Number of arguments.
         * @param array $argv Command line arguments.
         */
        public function parse($argc, $argv)
        {
                for ($i = 1; $i < $argc; ++$i) {
                        if (strstr($argv[$i], "=")) {
                                list($key, $val) = explode("=", $argv[$i]);
                        } else {
                                list($key, $val) = array($argv[$i], null);
                        }

                        switch ($key) {
                                case '--base':
                                        $this->_capture = new Capture($val);
                                        break;
                                case '-s':
                                case '--save':
                                        $this->_mode = self::MODE_SAVE;
                                        break;
                                case '-r':
                                case '--read':
                                        $this->_mode = self::MODE_READ;
                                        break;
                                case '-t':
                                case '--stat':
                                        $this->_mode = self::MODE_STAT;
                                        break;
                                case '-f':
                                case '--force':
                                        $this->_force = true;
                                        break;
                                case '-h':
                                case '--help':
                                        $this->usage();
                                        break;
                                default:
                                        $this->_pathes = array_slice($argv, $i);
                                        return;
                        }
                }
        }

        private function usage()
        {
                $script = basename($_SERVER['SCRIPT_FILENAME']);

                printf("%s - raw HTML capture tool\n", $script);
                printf("\n");
                printf("Usage: %s --base=url [--save] path1 [path2...]\n", $script);
                printf("Options:\n");
                printf("  --base=url:   The base URL for relative pathes.\n");
                printf("  -s,--save:    Save pathX content to pathX.inc.\n");
                printf("  -r,--read:    Read pathX content and output.\n");
                printf("  -t,--stat:    Stat pathX content and report.\n");
                printf("  -f,--force:   Force overwrite existing target file.\n");
                printf("  -h,--help:    This casual help.\n");
                printf("Example:\n");
                printf("  %s --base=http://localhost/sites/it.bmc.uu.se --save andlov/docs/apache/broker andlov/docs/apache/autoindex\n", $script);
                printf("\n");
                printf("Copyright (C) 2018 Anders Lövgren, Nowise Systems\n");
        }

}

// 
// Report missing functions:
// 
function missing($func)
{
        
}

// 
// Begin script execution:
// 
try {
        $application = new Application();
        $application->parse($_SERVER['argc'], $_SERVER['argv']);
        $application->process();
} catch (Exception $exception) {
        fprintf(STDERR, "(-) %s\n", $exception->getMessage());
}
