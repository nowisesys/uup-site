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

require_once(realpath(__DIR__ . '/../vendor/autoload.php'));

use UUP\Site\Utility\Config;

/**
 * Support class for site config.
 *
 * @author Anders Lövgren (Computing Department at BMC, Uppsala University)
 */
class Application
{

        /**
         * Output in dump format.
         */
        const OUTPUT_DUMP = 1;
        /**
         * Output in serialized format.
         */
        const OUTPUT_SERIALIZED = 2;
        /**
         * Output in array format.
         */
        const OUTPUT_ARRAY = 3;
        /**
         * Output in JSON format.
         */
        const OUTPUT_JSON = 4;

        /**
         * The config file.
         * @var string 
         */
        private $_config;
        /**
         * Site configuration.
         * @var Config 
         */
        private $_object;
        /**
         * Write config to stdout or file.
         * @var bool|string
         */
        private $_output = false;
        /**
         * Output format.
         * @var string 
         */
        private $_format = self::OUTPUT_DUMP;

        /**
         * Contructor.
         */
        public function __construct()
        {
                $this->_object = new Config(null, false, false);
        }

        /**
         * Parse command line options.
         * @param int $argc Number of arguments.
         * @param array $argv Command line arguments.
         */
        public function parse($argc, $argv)
        {
                for ($i = 1; $i < $argc; ++$i) {

                        if (strstr($argv[$i], '=')) {
                                list($key, $val) = explode('=', $argv[$i]);
                        } else {
                                list($key, $val) = array($argv[$i], null);
                        }

                        switch ($key) {
                                // 
                                // Common options:
                                // 
                                case "-c":
                                case "--config":
                                        if (!file_exists($val)) {
                                                fprintf(STDERR, "(-) The config file $val is missing.\n");
                                                exit(1);
                                        } else {
                                                $this->_config = $val;
                                                $this->_object = new Config($val, false, false);
                                        }
                                        break;
                                case "-o":
                                case "--output":
                                        if (isset($val)) {
                                                $this->_output = $val;
                                        } else {
                                                $this->_output = true;
                                        }
                                        break;
                                case "--format":
                                        switch ($val) {
                                                case 'dump':
                                                case 'var':
                                                        $this->_format = self::OUTPUT_DUMP;
                                                        break;
                                                case 'array':
                                                case 'arr':
                                                        $this->_format = self::OUTPUT_ARRAY;
                                                        break;
                                                case 'serialized':
                                                case 'serial':
                                                case 'ser':
                                                        $this->_format = self::OUTPUT_SERIALIZED;
                                                        break;
                                                case 'json':
                                                        $this->_format = self::OUTPUT_JSON;
                                                        break;
                                                default:
                                                        fprintf(STDERR, "Unknown format '$val', see --help\n");
                                                        exit(1);
                                        }
                                        break;
                                case "-h":
                                case "--help":
                                        $this->usage();
                                        exit(0);
                                // 
                                // Aliases:
                                // 
                                case "-D":
                                case "--dump":
                                        $this->_format = self::OUTPUT_DUMP;
                                        break;
                                case "-S":
                                case "--serialize":
                                        $this->_format = self::OUTPUT_SERIALIZED;
                                        break;
                                case "-A":
                                case "--array":
                                        $this->_format = self::OUTPUT_ARRAY;
                                        break;
                                case "-J":
                                case "--json":
                                        $this->_format = self::OUTPUT_JSON;
                                        break;
                                // 
                                // Config options:
                                // 
                                case "--css":
                                        $this->_object->css = $val;
                                        break;
                                case "--docs":
                                        $this->_object->docs = $val;
                                        break;
                                case "--exception":
                                        $this->_object->exception = $val;
                                        break;
                                case "--font":
                                        $this->_object->font = $val;
                                        break;
                                case "--footer":
                                        $this->_object->footer = $val;
                                        break;
                                case "--img":
                                        $this->_object->img = $val;
                                        break;
                                case "--js":
                                        $this->_object->js = $val;
                                        break;
                                case "--location":
                                        $this->_object->location = $val;
                                        break;
                                case "--name":
                                        $this->_object->name = $val;
                                        break;
                                case "--proj":
                                        $this->_object->proj = $val;
                                        break;
                                case "--root":
                                        $this->_object->root = $val;
                                        break;
                                case "--template":
                                        $this->_object->template = $val;
                                        break;
                                case "--theme":
                                        $this->_object->theme = $val;
                                        break;
                                default:
                                        fprintf(STDERR, "(-) Unknown option '$key', see --help\n");
                                        exit(1);
                        }
                }
        }

        /**
         * Display usage.
         */
        private function usage()
        {
                $script = basename($_SERVER['SCRIPT_FILENAME']);

                printf("%s - site config utility\n", $script);
                printf("\n");
                printf("Usage: %s [--config=file] [options...]\n", $script);
                printf("Options:\n");
                printf("  --config=file:   Use site config file\n");
                printf("  --output[=file]: Write file or stdout\n");
                printf("  --format=str:    Output format (dump, serialize or array)\n");
                printf("  -h,--help:       This casual help\n");
                printf("Aliases:\n");
                printf("  -S,--serialize:  Output serialized data\n");
                printf("  -A,--array:      Output array data\n");
                printf("  -D,--dump:       Output in dump format\n");
                printf("  -J,--json:       Output in JSON format\n");
                printf("Config:\n");
                printf("  --name=str:      The site name\n");

                printf("  --root=path:     The top directory (virtual host)\n");
                printf("  --docs=path:     The document root directory\n");
                printf("  --proj=path:     The project directory\n");
                printf("  --template=name: The template directory\n");
                printf("  --location=path: The URI location\n");

                printf("  --css=path:      The CSS location\n");
                printf("  --js=path:       The JS location\n");
                printf("  --img=path:      The images location\n");
                printf("  --font=path:     The fonts location\n");

                printf("  --theme=str:     The default theme\n");
                printf("\n");
                printf("Example:\n");
                printf("  # Dump default config to stdout:\n");
                printf("  bash> php admin/config.php --output\n");
                printf("\n");
                printf("Copyright (C) 2016 Anders Lövgren, Computing Department at BMC, Uppsala University\n");
        }

        public function process()
        {
                if ($this->_output) {
                        switch ($this->_format) {
                                case self::OUTPUT_ARRAY:
                                        $data = var_export($this->_object->getData(), true);
                                        break;
                                case self::OUTPUT_DUMP:
                                        $data = print_r($this->_object->getData(), true);
                                        break;
                                case self::OUTPUT_SERIALIZED:
                                        $data = serialize($this->_object->getData());
                                        break;
                                case self::OUTPUT_JSON:
                                        $data = json_encode($this->_object->getData());
                                        break;
                        }
                        if (is_string($this->_output)) {
                                file_put_contents($this->_output, $data);
                        } else {
                                print_r($data);
                        }
                }
        }

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
