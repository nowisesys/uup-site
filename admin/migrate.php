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

// 
// Script for migrating pages that uses the transitional template system.
// 
set_include_path(__DIR__);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

require_once(realpath(__DIR__ . '/../vendor/autoload.php'));

use UUP\Site\Page\Web\Migration\TransitionalPage;

/**
 * Conversion helper class.
 * @author Anders Lövgren (Computing Department at BMC, Uppsala University)
 */
class Convert
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
         * @param string $name The 
         * @param TransitionalPage $page The page object.
         */
        public function __construct($name, $page)
        {
                $this->_name = $name;
                $this->_page = $page;
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

}

/**
 * The migration application class.
 * @author Anders Lövgren (Computing Department at BMC, Uppsala University)
 */
class Application
{

        /**
         * Keep backup of old file.
         */
        const MODE_BACKUP = "backup";
        /**
         * Just output new file.
         */
        const MODE_OUTPUT = "output";
        /**
         * Write new file to stdout.
         */
        const MODE_STDOUT = "stdout";
        /**
         * Overwrite old file.
         */
        const MODE_REPLACE = "replace";

        /**
         * The source file.
         * @var string 
         */
        private $_source;
        /**
         * The target file.
         * @var string 
         */
        private $_target;
        /**
         * The backup file.
         * @var string 
         */
        private $_backup;
        /**
         * The page object.
         * @var TransitionalPage 
         */
        private $_page;
        /**
         * The migration mode.
         * @var string 
         */
        private $_mode = self::MODE_BACKUP;
        /**
         * Overwrite existing files.
         * @var bool 
         */
        private $_overwrite = false;
        /**
         * Generate code for autoload.
         * @var bool 
         */
        private $_autoload = false;

        /**
         * Constructor.
         * @param string $source The source file.
         */
        public function __construct($source)
        {
                $this->_source = $source;
                $this->_target = sprintf("%s.new", $source);
                $this->_backup = sprintf("%s.old", $source);

                $this->_page = new TransitionalPage($this->_source, false);
        }

        /**
         * Parse command line options.
         * @param int $argc Number of arguments.
         * @param array $argv Command line arguments.
         */
        public function parse($argc, $argv)
        {
                for ($i = 1; $i < $argc; ++$i) {
                        switch ($argv[$i]) {
                                case '-a':
                                case '--autoload':
                                        $this->_autoload = true;
                                        break;
                                case "-b":
                                case "--backup":
                                        $this->_mode = self::MODE_BACKUP;
                                        break;
                                case "-o":
                                case "--output":
                                        $this->_mode = self::MODE_OUTPUT;
                                        break;
                                case "-s":
                                case "--stdout":
                                case "-":
                                        $this->_mode = self::MODE_STDOUT;
                                        break;
                                case "-r":
                                case "--replace":
                                        $this->_mode = self::MODE_REPLACE;
                                        break;
                                case "-f":
                                case "--force":
                                case "--overwrite":
                                        $this->_overwrite = true;
                                        break;
                                case "-h":
                                case "--help":
                                        $this->usage();
                                        exit(0);
                        }
                }
        }

        /**
         * Display usage.
         */
        private function usage()
        {
                $script = basename($_SERVER['SCRIPT_FILENAME']);

                printf("%s - template system migration tool\n", $script);
                printf("\n");
                printf("Usage: %s page.php [options...]\n", $script);
                printf("Options:\n");
                printf("  -a,--autoload:   Generate code for autoload.\n");
                printf("  -b,--backup:     Keep backup of old file.\n");
                printf("  -o,--output:     Output to new file (*.new)\n");
                printf("  -s,--stdout:     Write converted file to standard out.\n");
                printf("  -r,--replace:    Replace old file with converted file.\n");
                printf("  -f,--force:\n");
                printf("     --overwrite:  Force overwrite existing target file.\n");
                printf("  -h,--help:       This casual help.\n");
                printf("Notice:\n");
                printf("  Use either one of the -b|-o|-s|-r mode options.\n");
                printf("\n");
                printf("Copyright (C) 2016-2018 Anders Lövgren, Computing Department at BMC, Uppsala University\n");
        }

        /**
         * Ugly hack for handling missing functions.
         */
        public function missing()
        {
                $file = sprintf("%s.missing", $this->_source);

                if (file_exists($file)) {
                        $data = array_unique(explode("\n", trim(file_get_contents($file))));

                        foreach ($data as $func) {
                                $todo = sprintf("//\n// TODO: Fix call to missing function %s\n//", $func);
                                $body = sprintf("trigger_error('Missing function %s called', E_USER_DEPRECATED);", $func);
                                $plug = sprintf("function %s\n{\n\t%s\n}", $func, $body);
                        }

                        file_put_contents($this->_target, sprintf("\n%s\n%s\n", $todo, $plug), FILE_APPEND);
                        unlink($file);
                }
        }

        /**
         * Convert source file.
         * @throws Exception
         */
        public function migrate()
        {
                chdir(dirname($this->_source));
                $this->convert();
                $this->finish();
        }

        /**
         * Perform actual conversion.
         * @throws Exception
         */
        private function convert()
        {
                if ($this->_overwrite) {
                        if (file_exists($this->_target)) {
                                unlink($this->_target);
                        }
                        if (file_exists($this->_backup)) {
                                unlink($this->_backup);
                        }
                }

                if (!file_exists($this->_source)) {
                        throw new Exception(sprintf("Source file %s don't exist", $this->_source));
                }
                if (file_exists($this->_target)) {
                        throw new Exception(sprintf("Target file %s exist. Refused to overwrite (override using --force)", $this->_target));
                }
                if (file_exists($this->_backup)) {
                        throw new Exception(sprintf("Backup file %s exist. Refused to overwrite (override using --force)", $this->_backup));
                }

                $inp = fopen($this->_source, "r");
                $out = fopen($this->_target, "w");

                if (!is_resource($inp)) {
                        throw new Exception(sprintf("Failed open %s for input", $this->_source));
                }
                if (!is_resource($out)) {
                        throw new Exception(sprintf("Failed open %s for output", $this->_target));
                }

                $convert = new Convert($this->getName(), $this->_page);
                $convert->autoload = $this->_autoload;
                $convert->write($inp, $out);

                // 
                // Close file handles:
                // 
                if (!fclose($inp)) {
                        throw new Exception(sprintf("Failed close %s for input", $this->_source));
                }
                if (!fclose($out)) {
                        throw new Exception(sprintf("Failed close %s for output", $this->_target));
                }
        }

        /**
         * Finish up.
         */
        private function finish()
        {
                switch ($this->_mode) {
                        case self::MODE_BACKUP:
                                $this->backup();
                                $this->replace();
                                break;
                        case self::MODE_OUTPUT:
                                $this->output();
                                break;
                        case self::MODE_REPLACE:
                                $this->replace();
                                break;
                        case self::MODE_STDOUT:
                                $this->stdout();
                                break;
                }
        }

        /**
         * Create backup copy.
         * @throws Exception
         */
        private function backup()
        {
                if (!copy($this->_source, $this->_backup)) {
                        throw new Exception(sprintf("Failed create backup file %s", $this->_backup));
                } else {
                        printf("[backup]:\t%s -> %s\n", $this->_source, $this->_backup);
                }
        }

        /**
         * Output converted file (already done).
         */
        private function output()
        {
                printf("[output]:\t%s\n", $this->_target);
        }

        /**
         * Write converted file to standard out.
         */
        private function stdout()
        {
                echo file_get_contents($this->_target);
                unlink($this->_target);
        }

        /**
         * Replace source file with converted.
         * @throws Exception
         */
        private function replace()
        {
                if (!rename($this->_target, $this->_source)) {
                        throw new Exception(sprintf("Failed replace source file %s", $this->_source));
                } else {
                        printf("[replace]:\t%s -> %s\n", $this->_target, $this->_source);
                        $this->_target = $this->_source;
                }
        }

        /**
         * Get class name.
         * @return string
         */
        private function getName()
        {
                $parts = explode('-', basename($this->_source));
                $parts = array_map('ucfirst', $parts);
                return str_replace(".php", "Page", implode('', $parts));
        }

}

// 
// Report missing functions:
// 
function missing($func)
{
        $file = sprintf("%s.missing", $_SERVER['argv'][1]);
        $data = sprintf("%s\n", $func);
        file_put_contents($file, $data, FILE_APPEND);
}

if (!function_exists('print_table_of_content')) {

        function print_table_of_content()
        {
                missing('print_table_of_content()');
        }

}
if (!function_exists('print_gallery_thumbs')) {

        function print_gallery_thumbs()
        {
                missing('print_gallery_thumbs()');
        }

}
if (!function_exists('print_subdir_index')) {

        function print_subdir_index()
        {
                missing('print_subdir_index()');
        }

}

// 
// Begin script execution:
// 
try {
        $application = new Application($_SERVER['argv'][1]);
        $application->parse($_SERVER['argc'], $_SERVER['argv']);
        $application->migrate();
        $application->missing();
} catch (Exception $exception) {
        fprintf(STDERR, "(-) %s\n", $exception->getMessage());
}
