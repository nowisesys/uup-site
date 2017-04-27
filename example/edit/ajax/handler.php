<?php

/*
 * Copyright (C) 2017 Anders Lövgren (QNET/BMC CompDept).
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

use UUP\Site\Request\Params;
use UUP\Site\Utility\Content\Iterator\Collector;
use UUP\Site\Utility\Content\Iterator\Context as ContextIterator;
use UUP\Site\Utility\Content\Iterator\Files as FilesIterator;
use UUP\Site\Utility\Content\Iterator\Menus as MenusIterator;

// 
// Site editor API backend classes.
// 

abstract class HandlerBase
{

        /**
         * The target directory.
         * @var string 
         */
        protected $_path;

        /**
         * Constructor.
         * @param string $path The target directory.
         */
        public function __construct($path)
        {
                $this->_path = $path;
        }

        /**
         * Read all files.
         * @param FilterIterator $iterator The file system iterator.
         */
        protected function listing($iterator)
        {
                $result = $this->collect($iterator);
                $this->send($result);
        }

        /**
         * Collect content from iterator.
         * @param FilterIterator $iterator The directory iterator.
         * @return array 
         */
        private function collect($iterator)
        {
                $collector = new Collector($iterator);
                return $collector->getContent();
        }

        /**
         * Send result.
         * @param mixed $result The result.
         */
        private function send($result = false)
        {
                echo json_encode(array(
                        'status' => 'success',
                        'result' => $result
                ));
        }

        /**
         * Create absolute path.
         * @param string $target The target file or directory.
         * @return string
         */
        protected function path($target)
        {
                if ($target === false) {
                        return $target;
                } elseif ($target[0] == '/') {
                        return $target;
                } else {
                        return sprintf("%s/%s", $this->_path, $target);
                }
        }

        /**
         * Read the file content.
         * @param string $source The filename.
         */
        private function read($source)
        {
                if (!$source) {
                        throw new RuntimeException(_("The target file is unset"));
                }
                if (!file_exists($source)) {
                        throw new RuntimeException(_("The target file is missing"));
                }
                if (is_dir($source)) {
                        throw new RuntimeException(_("The target file is a directory"));
                }
                if (!is_readable($source)) {
                        throw new RuntimeException(_("The target file is not readable"));
                }
                if (!readfile($source)) {
                        throw new RuntimeException(_("Failed read file"));
                }
        }

        /**
         * Create a new file.
         * @param string $source The source template.
         * @param string $target The target file.
         */
        protected function create($source, $target)
        {
                if (!$source) {
                        throw new RuntimeException(_("The target file is unset"));
                }
                if (file_exists($source)) {
                        throw new RuntimeException(_("The target file already exists"));
                }

                // TODO: implement
                die("Not yet implemented");
        }

        /**
         * Update file content.
         * @param string $target The target file.
         * @param string $content The file content.
         */
        private function update($target, $content = false)
        {
                if (!$target) {
                        throw new RuntimeException(_("The target file is unset"));
                }
                if (!file_exists($target)) {
                        throw new RuntimeException(_("The target file is missing"));
                }
                if (is_dir($target)) {
                        throw new RuntimeException(_("The target file is a directory"));
                }
                if (!is_writable($target)) {
                        throw new RuntimeException(_("The target file is not writable"));
                }
                if (!$content) {
                        $content = file_get_contents("php://stdin");
                }
                if (!$content) {
                        throw new RuntimeException(_("Refuse to truncate existing file (content input is empty)"));
                }
                if (!file_put_contents($target, $content)) {
                        throw new RuntimeException(_("Failed write file"));
                }
        }

        /**
         * Delete a file or directory.
         * @param string $target The target file.
         */
        private function delete($target)
        {
                if (!$target) {
                        throw new RuntimeException(_("The target file is unset"));
                }
                if (!file_exists($target)) {
                        throw new RuntimeException(_("The target file is missing"));
                }
                if (!is_writable($target)) {
                        throw new RuntimeException(_("The target file is not deletable"));
                }
                if (is_dir($target)) {
                        if (!rmdir($target)) {
                                throw new RuntimeException(_("Failed remove directory"));
                        }
                } else {
                        if (!unlink($target)) {
                                throw new RuntimeException(_("Failed unlink file"));
                        }
                }
        }

        /**
         * Rename a file.
         * @param string $source The source filename.
         * @param string $target The target filename.
         */
        private function rename($source, $target)
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
        private function move($source, $target)
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
         * @param string $source The source filename (linked file or directory).
         * @param string $target The target filename (the link name).
         */
        private function link($source, $target)
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

        /**
         * Copy a file.
         * @param string $source The source filename.
         * @param string $target The target file.
         */
        private function copy($source, $target)
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
                if (!copy($source, $target)) {
                        throw new RuntimeException(_("Failed rename file or directory"));
                }
        }

        /**
         * Process request.
         * @param Params $request The request parameters.
         */
        protected function process($request)
        {
                switch ($request->getParam('action')) {
                        case 'read':
                                $this->read($request->getParam('source'));
                                break;
                        case 'update':
                                $this->update($request->getParam('target'), $request->getParam('content'));
                                break;
                        case 'delete':
                                $this->delete($request->getParam('delete'));
                                break;
                        case 'rename':
                                $this->rename($request->getParam('source'), $request->getParam('target'));
                                break;
                        case 'move':
                                $this->move($request->getParam('source'), $request->getParam('target'));
                                break;
                        case 'link':
                                $this->link($request->getParam('source'), $request->getParam('target'));
                                break;
                        case 'copy':
                                $this->copy($request->getParam('source'), $request->getParam('target'));
                                break;
                }
        }

}

class FilesHandler extends HandlerBase
{

        /**
         * Process request.
         * @param Params $request The request parameters.
         */
        public function process($request)
        {
                $request->setFilter(array(
                        'action' => '/^(create|read|update|delete|rename|move|link|copy|list)$/'
                ));

                switch ($request->getParam('action')) {
                        case 'list':
                                parent::listing(new FilesIterator($this->_path));
                                break;
                        case 'create':
                                $request->addFilter(
                                    'source', '/^(secure-page|secure-view|standard-page|standard-view|router|directory)$/'
                                );
                                $this->create($request->getParam('source'), $request->getParam('target'));
                                break;
                        default:
                                parent::process($request);
                }
        }

        /**
         * Create new file or directory.
         * @param string $source The source template.
         * @param string $target The target file.
         */
        protected function create($source, $target)
        {
                $source = $this->template($source);
                $target = $this->path($target);

                if (isset($source)) {
                        parent::create($source, $target);
                } else {
                        $this->mkdir($target);
                }
        }

        /**
         * Get template file.
         * @param string $source The template name.
         * @return string
         */
        private function template($source)
        {
                $sources = array(
                        'secure-page'   => __DIR__ . '../templates/files/secure/page.phpt',
                        'secure-view'   => __DIR__ . '../templates/files/secure/view.phpt',
                        'standard-page' => __DIR__ . '../templates/files/standard/page.phpt',
                        'standard-view' => __DIR__ . '../templates/files/standard/view.phpt',
                        'router'        => __DIR__ . '../templates/files/standard/router.phpt',
                        'directory'     => null
                );

                return $sources[$source];
        }

        /**
         * Create new directory.
         * @param string $target The target directory.
         */
        private function mkdir($target)
        {
                if (file_exists($target)) {
                        throw new RuntimeException(_("The target directory exists"));
                }
                if (!mkdir($target)) {
                        throw new RuntimeException(_("Failed create directory"));
                }
        }

}

class MenusHandler extends HandlerBase
{

        /**
         * Process request.
         * @param Params $request The request parameters.
         */
        public function process($request)
        {
                $request->setFilter(array(
                        'action' => '/^(create|read|update|delete|move|link|copy|list|add|remove)$/'
                ));

                switch ($request->getParam('action')) {
                        case 'list':
                                parent::listing(new MenusIterator($this->_path));
                                break;
                        case 'create':
                                $request->addFilter(
                                    'source', '/^(sidebar|standard|topbar)$/'
                                );
                                $this->create($request->getParam('source'), $request->getParam('target'));
                                break;
                        case 'add':
                                break;
                        case 'remove':
                                break;
                        default:
                                parent::process($request);
                }
        }

        /**
         * Create new file.
         * @param string $source The source template.
         * @param string $target The target file.
         */
        protected function create($source, $target)
        {
                $source = $this->template($source);
                $target = $this->path($target);

                parent::create($source, $target);
        }

        /**
         * Get template file.
         * @param string $source The template name.
         * @return string
         */
        private function template($source)
        {
                $sources = array(
                        'sidebar'  => __DIR__ . '../templates/menus/sidebar.menu',
                        'standard' => __DIR__ . '../templates/menus/standard.menu',
                        'topbar'   => __DIR__ . '../templates/menus/topbar.menu'
                );

                return $sources[$source];
        }

}

class ContextHandler extends HandlerBase
{

        /**
         * Process request.
         * @param Params $request The request parameters.
         */
        public function process($request)
        {
                $request->setFilter(array(
                        'action' => '/^(create|read|update|delete|move|link|copy|list)$/'
                ));

                switch ($request->getParam('action')) {
                        case 'list':
                                parent::listing(new ContextIterator($this->_path));
                                break;
                        case 'create':
                                $request->addFilter(
                                    'source', '/^(content|headers|publish)$/'
                                );
                                $this->create($request->getParam('source'), $request->getParam('target'));
                                break;
                        default:
                                parent::process($request);
                }
        }

        /**
         * Create new file.
         * @param string $source The source template.
         * @param string $target The target file.
         */
        protected function create($source, $target)
        {
                $source = $this->template($source);
                $target = $this->path($target);

                parent::create($source, $target);
        }

        /**
         * Get template file.
         * @param string $source The template name.
         * @return string
         */
        private function template($source)
        {
                $sources = array(
                        'content' => __DIR__ . '../templates/context/content.spec',
                        'headers' => __DIR__ . '../templates/context/headers.inc',
                        'publish' => __DIR__ . '../templates/context/publish.inc'
                );

                return $sources[$source];
        }

}
