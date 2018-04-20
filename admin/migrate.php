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

require_once('include/convert/page.inc');
require_once('include/convert/publish.inc');
require_once('include/convert/content-spec.inc');
require_once('include/convert/sidebar-menu.inc');
require_once('include/convert/standard-menu.inc');
require_once('include/convert/topbar-menu.inc');

/**
 * The conversion helper class.
 */
class Convert
{

        /**
         * The source file.
         * @var string 
         */
        private $_source;
        /**
         * Generate code for autoload.
         * @var bool 
         */
        private $_autoload = false;

        /**
         * Constructor.
         * @param string $source The source file.
         * @param bool $autoload The autoload setting.
         */
        public function __construct($source, $autoload)
        {
                $this->_source = $source;
                $this->_autoload = $autoload;
        }

        public function process($inp, $out, $type)
        {
                if ($type == "detect") {
                        $type = basename($this->_source);
                }

                switch ($type) {
                        case 'publish':
                        case 'publish.inc':
                                $convert = new Publish($this->_source);
                                break;
                        case 'topbar':
                        case 'topbar.menu':
                                $convert = new TopbarMenu($this->_source);
                                break;
                        case 'menu':
                        case 'standard.menu':
                                $convert = new StandardMenu($this->_source);
                                break;
                        case 'sidebar':
                        case 'sidebar.menu':
                                $convert = new SidebarMenu($this->_source);
                                break;
                        case 'content':
                        case 'content.spec':
                                $convert = new ContentSpec($this->_source);
                                break;
                        case 'page':
                        default:
                                $convert = new Page($this->_source);
                                $convert->autoload = $this->_autoload;
                                break;
                }

                try {
                        $convert->write($inp, $out);
                } catch (Exception $exception) {
                        fprintf(STDERR, $exception->getMessage() . "\n");
                }
        }

}

/**
 * The migration application class.
 * 
 * Migration of content/menus are ambigous because content.spec in old format 
 * contains both content spec and standard menu (urls) in same file. Use the
 * type options to resolve this issue.
 * 
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
         * Convert source page (*.php).
         */
        const TYPE_PAGE = "page";
        /**
         * Convert content spec.
         */
        const TYPE_CONTENT = "content";
        /**
         * Convert publish information.
         */
        const TYPE_PUBLISH = "publish";
        /**
         * Convert sidebar menu.
         */
        const TYPE_SIDEBAR = "sidebar";
        /**
         * Convert topbar menu.
         */
        const TYPE_TOPBAR = "topbar";
        /**
         * Convert standard menu.
         */
        const TYPE_MENU = "menu";
        /**
         * Try to detect conversion.
         */
        const TYPE_DETECT = "detect";

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
         * The migration mode.
         * @var string 
         */
        private $_mode = self::MODE_BACKUP;
        /**
         * The conversion type.
         * @var string 
         */
        private $_type = self::TYPE_DETECT;
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
                                case "--type":
                                        $this->_type = $val;
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
                printf("Usage: %s inputfile [options...]\n", $script);
                printf("Options:\n");
                printf("  -a,--autoload:   Generate code for autoload.\n");
                printf("  -b,--backup:     Keep backup of old file.\n");
                printf("  -o,--output:     Output to new file (*.new)\n");
                printf("  -s,--stdout:     Write converted file to standard out.\n");
                printf("  -r,--replace:    Replace old file with converted file.\n");
                printf("     --type=name:  The type of input (page, content, publish, sidebar, topbar or menu)\n");
                printf("  -f,--force:\n");
                printf("     --overwrite:  Force overwrite existing target file.\n");
                printf("  -h,--help:       This casual help.\n");
                printf("Notice:\n");
                printf("  * Use either one of the -b|-o|-s|-r mutual exclusion mode options.\n");
                printf("  * The input file is either:\n");
                printf("    1. PHP source code file (*.php)\n");
                printf("    2. Content specification (content.spec)\n");
                printf("    3. Page publisher (publish.inc)\n");
                printf("    4. Menu file (sidebar.menu, standard.menu or topbar.menu\n");
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

                $convert = new Convert($this->_source, $this->_autoload);
                $convert->process($inp, $out, $this->_type);

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
