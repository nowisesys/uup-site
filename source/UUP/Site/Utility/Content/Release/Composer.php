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
 * Update composer package index.
 * 
 * Assumes a directory structure containing stable (release ready versions) and 
 * testing (trunk from version control):
 * 
 * <pre>
 * package/
 *   +-- stable/
 *   +-- testing
 * </pre>
 * 
 * This default layout can be overridden by setting the maps property:
 * 
 * <code>
 * $composer->maps = [
 *      'develop' => 'dev',
 *      'release' => ''
 * ];
 * $composer->update();
 * </code>
 * 
 * @property-write string $name The package name.
 * @property-write string $dist The package dist URL.
 * @property-write array $maps The location map.
 *
 * @author Anders Lövgren (Computing Department at BMC, Uppsala University)
 */
class Composer
{

        /**
         * The root directory.
         * @var string 
         */
        private $_root;
        /**
         * The package name.
         * @var string 
         */
        private $_name;
        /**
         * The package dist URL.
         * @var string 
         */
        private $_dist;
        /**
         * The location map.
         * @var array 
         */
        private $_maps = array('testing' => 'dev', 'stable' => '');

        /**
         * Constructor.
         * @param string $name The package name.
         * @param string $dist The package dist URL.
         */
        public function __construct($name = "", $dist = "")
        {
                if (PHP_SAPI != 'cli') {
                        die("This script should be runned in CLI mode.\n");
                }

                $this->_dist = $dist;
                $this->_name = $name;
                $this->_root = getcwd();
        }

        public function __set($name, $value)
        {
                switch ($name) {
                        case 'dist':
                                $this->_dist = (string) $value;
                                break;
                        case 'name':
                                $this->_name = (string) $value;
                                break;
                        case 'maps':
                                $this->_maps = (array) $value;
                                break;
                }
        }

        /**
         * Update packages.json file. 
         */
        public function update()
        {
                echo "(i) updating composer package ($this->_name)\n";

                try {
                        if (!($name = $this->_name)) {
                                die("(-) error: The name property is empty\n");
                        }
                        if (!($dist = $this->_dist)) {
                                die("(-) error: The dist property is empty\n");
                        }

                        $packages = array();

                        foreach ($this->_maps as $subdir => $tag) {
                                $this->processDirectory($packages, $subdir, $tag, $name, $dist);
                        }

                        uksort($packages['packages'][$name], function($a, $b) {
                                return strnatcmp($a, $b);
                        });

                        file_put_contents('packages.json', json_encode($packages, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
                } finally {
                        chdir($this->_root);
                }
        }

        private function processDirectory(&$packages, $subdir, $tag, $name, $dist)
        {
                try {
                        chdir($subdir);

                        if (($handle = opendir('.')) !== false) {
                                while ($file = readdir($handle)) {
                                        if ($file == "." || $file == "..") {
                                                continue;
                                        }
                                        $matches = array();
                                        if (preg_match("/(.*?-([\.0-9]+)).tar.gz$/", $file, $matches)) {
                                                $this->processPackage($packages, $subdir, $matches, $tag, $name, $dist);
                                        }
                                }
                                closedir($handle);
                        } else {
                                die("(-) error: failed open directory $subdir\n");
                        }
                } finally {
                        chdir("..");
                }
        }

        private function processPackage(&$packages, $subdir, $matches, $tag, $name, $dist)
        {
                $cmdout = array();
                $status = 1;

                $tgz = $matches[0];
                $pkg = $matches[1];
                $ver = $matches[2];
                $zip = sprintf("%s.zip", $pkg);

                if (!empty($tag)) {
                        $ver .= "-$tag";
                }

                exec("tar xfvzp $tgz $pkg", $cmdout, $status);
                if ($status != 0) {
                        die("(-) error: failed extract archive\n");
                } else {
                        echo "(i) extracted $tgz to $pkg\n";
                }

                try {
                        chdir($pkg);

                        exec("zip -r ../$zip *", $cmdout, $status);
                        if ($status != 0) {
                                die("(-) error: failed create zip-archive\n");
                        } else {
                                echo "(i) creating zip $zip for $ver:$name\n";
                        }

                        if (!file_exists('composer.json')) {
                                echo "(!) notice: $ver:$name has no composer.json\n";
                        } elseif (!($package = json_decode(file_get_contents('composer.json'), true))) {
                                echo "(-) error: failed decode composer.json\n";
                        } else {
                                $package['dist'] = array(
                                        'url'  => sprintf("%s/%s/%s", $dist, $subdir, $zip),
                                        'type' => 'zip'
                                );
                                $packages['packages'][$name][$ver] = $package;
                        }
                } finally {
                        chdir("..");
                }

                if (file_exists($pkg)) {
                        echo "(i) removing directory $pkg\n";
                        exec("rm -rf $pkg");
                }
        }

}
