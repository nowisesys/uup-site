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

require_once(realpath(__DIR__ . '/../../../vendor/autoload.php'));

use UUP\Site\Page\Service\SecureService;
use UUP\Site\Utility\Content\Iterator\Context as ContextIterator;
use UUP\Site\Utility\Content\Iterator\Files as FilesIterator;
use UUP\Site\Utility\Content\Iterator\Menus as MenusIterator;

/**
 * The index AJAX service.
 * 
 * The AJAX method API: 
 * ---------------------
 * o) action=read&source={files|menus|context}  // Read files, menus or context content.
 * 
 * This class also provides common files and directory actions for sub-classes. The path 
 * parameter is mandatory and should be relative to project root.
 * 
 * @author Anders Lövgren (QNET/BMC CompDept)
 * @package UUP
 * @subpackage Site
 */
class IndexPage extends SecureService
{

        /**
         * The requested action.
         * @var string 
         */
        protected $_action;
        /**
         * The source file.
         * @var string 
         */
        protected $_source;
        /**
         * The target file.
         * @var string 
         */
        protected $_target;
        /**
         * The target directory.
         * @var string 
         */
        protected $_path;

        public function __construct()
        {
                parent::__construct();
                
                if (!in_array($this->session->user, $this->config->edit['user'])) {
                        throw new RuntimeException('Caller is not an page/site editor');
                }

                $this->params->setFilter(array(
                        'action' => '/^(read)$/',
                        'source' => '/^(files|menus|context)$/'
                ));

                $this->_action = $this->params->getParam('action');
                $this->_source = $this->params->getParam('source');
                $this->_target = $this->params->getParam('target');

                $this->_path = $this->params->getParam('path');

                if (!$this->_path) {
                        throw new RuntimeException(_("Required target directory parameter is missing"));
                }
                if (!$this->_action) {
                        throw new RuntimeException(_("Required action parameter is missing"));
                }
                if ($this->_path[0] == '/') {
                        throw new RuntimeException(_("Absolute pathes is not allowed"));
                }
                if (strstr($this->_path, '..')) {
                        throw new RuntimeException(_("Directory navigation is not allowed"));
                }
                
                $this->_path = realpath($this->config->proj . '/' . $this->_path);
        }

        /**
         * RuntimeException handler.
         * @param RuntimeException $exception The exception to report.
         */
        public function onException($exception)
        {
                echo json_encode(array(
                        'status'  => 'failure',
                        'message' => $exception->getMessage(),
                        'code'    => $exception->getCode()
                ));
        }

        public function render()
        {
                if ($this->_action == 'read') {
                        switch ($this->_source) {
                                case 'files':
                                        $this->files($this->_path);
                                        break;
                                case 'menus':
                                        $this->menus($this->_path);
                                        break;
                                case 'context':
                                        $this->context($this->_path);
                                        break;
                        }
                }
        }

        /**
         * Read all files.
         * @param string $path The source path.
         */
        private function files($path)
        {
                $result = $this->collect(new FilesIterator($path));
                $this->send($result);
        }

        /**
         * Read all files.
         * @param string $path The source path.
         */
        private function menus($path)
        {
                $result = $this->collect(new MenusIterator($path));
                $this->send($result);
        }

        /**
         * Read all files.
         * @param string $path The source path.
         */
        private function context($path)
        {
                $result = $this->collect(new ContextIterator($path));
                $this->send($result);
        }

        /**
         * Collect content from iterator.
         * @param FilterIterator $iterator The directory iterator.
         */
        private function collect($iterator)
        {
                $result = array('dir' => array(), 'file' => array());

                foreach ($iterator as $fileinfo) {
                        if ($fileinfo->isDir()) {
                                $result['dir'][] = array(
                                        'name'  => $fileinfo->getFilename(),
                                        'link'  => null,
                                        'owner' => posix_getpwuid($fileinfo->getOwner())['name'],
                                        'size'  => 0
                                );
                        } elseif ($fileinfo->isLink()) {
                                $result['file'][] = array(
                                        'name'  => $fileinfo->getFilename(),
                                        'link'  => $fileinfo->getLinkTarget(),
                                        'owner' => posix_getpwuid($fileinfo->getOwner())['name'],
                                        'size'  => 0
                                );
                        } else {
                                $result['file'][] = array(
                                        'name'  => $fileinfo->getFilename(),
                                        'link'  => null,
                                        'owner' => posix_getpwuid($fileinfo->getOwner())['name'],
                                        'size'  => $fileinfo->getSize()
                                );
                        }
                }

                return $result;
        }

        /**
         * Send result.
         * @param mixed $result The result.
         */
        protected function send($result = false)
        {
                echo json_encode(array(
                        'status' => 'success',
                        'result' => $result
                ));
        }

        /**
         * Read the file content.
         * @param string $file The filename.
         */
        protected function read($file)
        {
                if (!$file) {
                        throw new RuntimeException(_("The target file is unset"));
                }
                if (!file_exists($file)) {
                        throw new RuntimeException(_("The target file is missing"));
                }
                if (is_dir($file)) {
                        throw new RuntimeException(_("The target file is a directory"));
                }
                if (!is_readable($file)) {
                        throw new RuntimeException(_("The target file is not readable"));
                }
                if (!readfile($file)) {
                        throw new RuntimeException(_("Failed read file"));
                }
        }

        /**
         * Create a new file.
         * @param string $file The filename.
         * @param string $type The template file.
         */
        protected function create($file, $type)
        {
                if (!$file) {
                        throw new RuntimeException(_("The target file is unset"));
                }
                if (file_exists($file)) {
                        throw new RuntimeException(_("The target file already exists"));
                }

                // TODO: implement
                die("Not yet implemented");
        }

        /**
         * Update file content.
         * @param string $file The filename.
         * @param string $content The file content.
         */
        protected function update($file, $content = null)
        {
                if (!$file) {
                        throw new RuntimeException(_("The target file is unset"));
                }
                if (!file_exists($file)) {
                        throw new RuntimeException(_("The target file is missing"));
                }
                if (is_dir($file)) {
                        throw new RuntimeException(_("The target file is a directory"));
                }
                if (!is_writable($file)) {
                        throw new RuntimeException(_("The target file is not writable"));
                }
                if (!isset($content)) {
                        $content = file_get_contents("php://stdin");
                }
                if (!file_put_contents($file, $content)) {
                        throw new RuntimeException(_("Failed write file"));
                }
        }

        /**
         * Delete a file or directory.
         * @param string $file The filename.
         */
        protected function delete($file)
        {
                if (!$file) {
                        throw new RuntimeException(_("The target file is unset"));
                }
                if (!file_exists($file)) {
                        throw new RuntimeException(_("The target file is missing"));
                }
                if (!is_writable($file)) {
                        throw new RuntimeException(_("The target file is not deletable"));
                }
                if (is_dir($file)) {
                        if (!rmdir($file)) {
                                throw new RuntimeException(_("Failed remove directory"));
                        }
                } else {
                        if (!unlink($file)) {
                                throw new RuntimeException(_("Failed unlink file"));
                        }
                }
        }

        /**
         * Rename a file.
         * @param string $source The source filename.
         * @param string $target The target filename.
         */
        protected function rename($source, $target)
        {
                if (!$source) {
                        throw new RuntimeException(_("The source file is unset"));
                }
                if (!$target) {
                        throw new RuntimeException(_("The target file is unset"));
                }
                if (!file_exists($source)) {
                        throw new RuntimeException(_("The source file is missing"));
                }
                if (file_exists($target)) {
                        throw new RuntimeException(_("The target file exists"));
                }
                if (!rename($source, $target)) {
                        throw new RuntimeException(_("Failed rename file or directory"));
                }
        }

        /**
         * Move a file.
         * @param string $source The source filename.
         * @param string $target The target directory.
         */
        protected function move($source, $target)
        {
                if (!$source) {
                        throw new RuntimeException(_("The source file is unset"));
                }
                if (!$target) {
                        throw new RuntimeException(_("The target file is unset"));
                }
                if (!file_exists($source)) {
                        throw new RuntimeException(_("The source file is missing"));
                }
                if (!file_exists($target)) {
                        throw new RuntimeException(_("The target directory is missing"));
                }
                if (!is_dir($target)) {
                        throw new RuntimeException(_("The target is not a directory"));
                }
                if (!rename($source, sprintf("%s/%s", $target, $source))) {
                        throw new RuntimeException(_("Failed move file to directory"));
                }
        }

        /**
         * Create symbolic link.
         * @param string $source The source filename.
         * @param string $target The target filename.
         */
        protected function link($source, $target)
        {
                if (!$source) {
                        throw new RuntimeException(_("The source file is unset"));
                }
                if (!$target) {
                        throw new RuntimeException(_("The target file is unset"));
                }
                if (!file_exists($source)) {
                        throw new RuntimeException(_("The source file is missing"));
                }
                if (file_exists($target)) {
                        throw new RuntimeException(_("The target file exists"));
                }
                if (!symlink($source, $target)) {
                        throw new RuntimeException(_("Failed create link"));
                }
        }

}

$page = new IndexPage();
$page->render();
