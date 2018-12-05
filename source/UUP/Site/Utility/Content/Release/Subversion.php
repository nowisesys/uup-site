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

namespace UUP\Site\Utility\Content\Release;

/**
 * Make subversion release.
 * 
 * Update stable and testing sub directories from SVN repository. This is 
 * code from 2008 with slightly modifications only.
 * 
 * @property-write string $host The SVN project URL.
 * @property-write string $name The project name.
 *
 * @author Anders Lövgren (Computing Department at BMC, Uppsala University)
 */
class Subversion
{

        /**
         * The root directory.
         * @var string 
         */
        private $_root;
        /**
         * The SVN project URL.
         * @var string 
         */
        private $_host;
        /**
         * The project name.
         * @var string 
         */
        private $_name;
        /**
         * Files to check timestamp on.
         * @var array 
         */
        private $_check = array("composer.json", "README", "README.txt", "AUTHORS", "INSTALL", "NEWS", "ChangeLog");

        /**
         * Constructor.
         * @param string $host The SVN project URL.
         * @param string $name The project name.
         */
        public function __construct($host = "", $name = "")
        {
                if (PHP_SAPI != 'cli') {
                        die("This script should be runned in CLI mode.\n");
                }

                $this->_host = $host;
                $this->_name = $name;
                $this->_root = getcwd();
        }

        public function __set($name, $value)
        {
                switch ($name) {
                        case 'host':
                                $this->_host = (string) $value;
                                break;
                        case 'name':
                                $this->_name = (string) $value;
                                break;
                }
        }

        /**
         * Make SVN release.
         * @param array $check Additional files to get release date from.
         */
        public function update($check = array())
        {
                if (!$this->_host) {
                        die("(-) error: The host property is empty\n");
                }
                if (!$this->_name) {
                        die("(-) error: The name property is empty\n");
                }

                echo "(i) updating subversion release ($this->_name)\n";

                if (count($check) != 0) {
                        $this->_check = array_merge($this->_check, $check);
                }
                if (file_exists('stable')) {
                        $this->updateStable();
                }
                if (file_exists('testing')) {
                        $this->updateTesting();
                }
        }

        private function updateStable()
        {
                try {
                        $check = $this->_check;

                        $subdir = sprintf("%s/stable", getcwd());
                        if (!file_exists($subdir)) {
                                echo "(i) creating directory $subdir\n";
                                if (!mkdir($subdir)) {
                                        die("(-) error: failed create directory $subdir\n");
                                }
                        }
                        if (!@chdir($subdir)) {
                                die("(-) error: failed change directory to $subdir\n");
                        }

                        // 
                        // Get all releases:
                        // 
                        $tags = array();
                        $status = 1;
                        exec(sprintf("svn ls %s/%s/tags", $this->_host, $this->_name), $tags, $status);
                        if ($status != 0) {
                                die("(-) error: failed get releases\n");
                        }
                        for ($i = 0; $i < count($tags); $i++) {
                                $tags[$i] = str_replace("/", "", $tags[$i]);
                        }
                        natsort($tags);

                        // 
                        // See if any new release exist. If so, export it from subversion:
                        // 
                        $cmdout = array();
                        foreach ($tags as $release) {
                                list($name, $version) = explode("-", $release);
                                $dest = sprintf("%s-%s", $this->_name, $version);
                                $time = 0;
                                $archive = sprintf("%s.tar.gz", $dest);
                                if (!file_exists($archive) && !file_exists($dest)) {
                                        printf("(i) exporting %s -> %s\n", $release, $dest);
                                        exec(sprintf("svn export %s/%s/tags/%s %s", $this->_host, $this->_name, $release, $dest), $cmdout, $status);
                                        if ($status != 0) {
                                                die(sprintf("(-) error: failed export %s\n", $release));
                                        }

                                        // 
                                        // Get release date from all files or those supplied by caller:
                                        // 
                                        if (isset($check) && count($check) != 0) {
                                                foreach ($check as $file) {
                                                        if (file_exists("$dest/$file")) {
                                                                $tnew = filemtime("$dest/$file");
                                                                if ($tnew > $time) {
                                                                        echo "(i) using $dest/$file as release date source.\n";
                                                                        $time = $tnew;
                                                                }
                                                        }
                                                }
                                        } else {
                                                $time = self::getReleaseTime($dest, $time);
                                        }

                                        if ($time == 0) {
                                                die("(-) error: failed get release date.\n");
                                        }
                                }
                                if (!file_exists($archive)) {
                                        echo "(i) creating archive $archive\n";
                                        exec("tar cfvz $archive $dest", $cmdout, $status);
                                        if ($status != 0) {
                                                die("(-) error: failed archive directory $dest\n");
                                        }
                                        touch($archive, $time);
                                }
                                if (file_exists($dest)) {
                                        echo "(i) removing directory $dest\n";
                                        exec("rm -rf $dest");
                                }
                        }

                        if (!isset($archive)) {
                                return;
                        }

                        // 
                        // Create link to latest release.
                        // 
                        $symlink = "latest.tar.gz";
                        if (file_exists($symlink)) {
                                unlink($symlink);
                        }
                        echo "(i) create symlink to $archive\n";
                        symlink($archive, "latest.tar.gz");
                } finally {
                        chdir($this->_root);
                }
        }

        private function updateTesting()
        {
                try {
                        $subdir = sprintf("%s/testing", getcwd());
                        if (!file_exists($subdir)) {
                                echo "(i) creating directory $subdir\n";
                                if (!mkdir($subdir)) {
                                        die("(-) error: failed create directory $subdir\n");
                                }
                        }
                        if (!@chdir($subdir)) {
                                die("(-) error: failed change directory to $subdir\n");
                        }

                        $date = date("Ymd");
                        $dest = sprintf("%s-%s", $this->_name, $date);
                        $archive = sprintf("%s.tar.gz", $dest);

                        $cmdout = array();
                        $status = 1;
                        if (!file_exists($dest)) {
                                exec(sprintf("svn export %s/%s/trunk %s", $this->_host, $this->_name, $dest), $cmdout, $status);
                                if ($status != 0) {
                                        die("(-) error: failed export trunk\n");
                                }
                        }
                        if (!file_exists($archive)) {
                                echo "(i) creating archive $archive\n";
                                exec("tar cfvz $archive $dest", $cmdout, $status);
                                if ($status != 0) {
                                        die("(-) error: failed archive directory $dest\n");
                                }
                        }
                        if (file_exists($dest)) {
                                echo "(i) removing directory $dest\n";
                                exec("rm -rf $dest");
                        }
                        $symlink = "latest.tar.gz";
                        if (file_exists($symlink)) {
                                unlink($symlink);
                        }
                        echo "(i) create symlink to $archive\n";
                        symlink($archive, "latest.tar.gz");
                } finally {
                        chdir($this->_root);
                }
        }

        private static function getReleaseTime($dest, &$time)
        {
                if (($handle = opendir($dest))) {
                        while ($file = readdir($handle)) {
                                if ($file == "." || $file == "..") {
                                        continue;
                                }
                                $path = sprintf("%s/%s", $dest, $file);
                                if (is_dir($path)) {
                                        $time = self::getReleaseTime($path, $time);
                                } elseif (is_link($path)) {
                                        continue;     // skip symbolic links
                                } elseif (is_file($path)) {
                                        $tnew = filemtime($path);
                                        if ($tnew > $time) {
                                                $time = $tnew;
                                        }
                                }
                        }
                        closedir($handle);
                }
                return $time;
        }

}
