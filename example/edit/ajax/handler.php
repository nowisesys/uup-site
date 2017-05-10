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
use UUP\Site\Utility\Content\Template;

// 
// Site editor API backend classes.
// 

abstract class HandlerBase
{

        /**
         * The public docs directory.
         * @var string 
         */
        private $_docs;
        /**
         * The current working path (relative public docs).
         * @var string 
         */
        private $_path;
        /**
         * The calling user.
         * @var string 
         */
        private $_user;

        /**
         * Constructor.
         * @param string $docs The public docs directory.
         * @param string $path The current working path.
         */
        public function __construct($docs, $path)
        {
                $this->_docs = $docs;
                $this->_path = $path;
        }

        /**
         * Set calling user.
         * @param string $user The username.
         */
        public function setUser($user)
        {
                $this->_user = $user;
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
        protected function send($result = true)
        {
                echo json_encode(array(
                        'status' => 'success',
                        'result' => $result
                ));
        }

        /**
         * Stat the file or directory content.
         * @param string $source The filename or directory.
         */
        private function stat($source)
        {
                if (!$source) {
                        throw new RuntimeException(_("The target file is unset"));
                }
                if (!file_exists($source)) {
                        throw new RuntimeException(_("The target file is missing"));
                }
                if (!is_readable($source)) {
                        throw new RuntimeException(_("The target file is not readable"));
                }
                if (($stat = stat($source))) {
                        // 
                        // Detect MIME type using magic database:
                        // 
                        if (!isset($stat['mime'])) {
                                if (($finfo = finfo_open(FILEINFO_MIME_TYPE))) {
                                        $stat['mime'] = finfo_file($finfo, $source);
                                        finfo_close($finfo);
                                } else {
                                        $stat['mime'] = mime_content_type($source);
                                }
                        }

                        // 
                        // Detect MIME type using web server headers:
                        // 
                        if ($stat['mime'] == 'text/plain') {
                                stream_context_set_default(
                                    array(
                                            'http' => array(
                                                    'method' => 'HEAD'
                                            )
                                    )
                                );

                                $host = filter_input(INPUT_SERVER, 'SERVER_NAME');
                                $path = substr($source, strlen($this->_docs));

                                $fileurl = sprintf("http://%s/%s", $host, $path);
                                $headers = get_headers($fileurl, 1);

                                if (array_key_exists('Content-Type', $headers)) {
                                        $stat['mime'] = $headers['Content-Type'];
                                }
                        }

                        // 
                        // Strip encoding (i.e. text/html;UTF-8) from MIME type:
                        // 
                        if (($pos = strpos($stat['mime'], ';')) !== false) {
                                $stat['mime'] = substr($stat['mime'], 0, $pos);
                        }

                        $result = array(
                                'mime'  => $stat['mime'],
                                'nlink' => $stat['nlink'],
                                'uid'   => $stat['uid'],
                                'gid'   => $stat['gid'],
                                'size'  => $stat['size'],
                                'mtime' => $stat['mtime'],
                        );
                        $this->send($result);
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
                if (readfile($source) === false) {
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
                if (file_exists($target)) {
                        throw new RuntimeException(_("The target file already exists"));
                }

                $template = new Template();
                $template->target = $target;
                $template->source = $source;
                $template->author = $this->_user;
                $template->name = $template->camelize($target);
                $template->output();

                $this->send();
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
                        $content = file_get_contents("php://input");
                }
                if (!$content) {
                        throw new RuntimeException(_("Refuse to truncate existing file (content input is empty)"));
                }
                if (!($bytes = file_put_contents($target, $content)) !== false) {
                        throw new RuntimeException(_("Failed write file"));
                } else {
                        $this->send($bytes);
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
                        } else {
                                $this->send();
                        }
                } else {
                        if (!unlink($target)) {
                                throw new RuntimeException(_("Failed unlink file"));
                        } else {
                                $this->send();
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
                } else {
                        $this->send();
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
                } else {
                        $this->send();
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
                } else {
                        $this->send();
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
                } else {
                        $this->send();
                }
        }

        /**
         * Process request.
         * @param Params $request The request parameters.
         */
        protected function process($request)
        {
                switch ($request->getParam('action')) {
                        case 'stat':
                                $this->stat($this->path($request->getParam('source')));
                                break;
                        case 'read':
                                $this->read($this->path($request->getParam('source')));
                                break;
                        case 'update':
                                $this->update($this->path($request->getParam('target')), $request->getParam('content'));
                                break;
                        case 'delete':
                                $this->delete($this->path($request->getParam('target')));
                                break;
                        case 'rename':
                                $this->rename($this->path($request->getParam('source')), $this->path($request->getParam('target')));
                                break;
                        case 'move':
                                $this->move($this->path($request->getParam('source')), $this->path($request->getParam('target')));
                                break;
                        case 'link':
                                $this->link($this->path($request->getParam('source')), $this->path($request->getParam('target')));
                                break;
                        case 'copy':
                                $this->copy($this->path($request->getParam('source')), $this->path($request->getParam('target')));
                                break;
                }
        }

        /**
         * Get absolute path.
         * @param string $target The target file or directory.
         * @return string
         */
        protected function path($target = null)
        {
                if (!isset($target)) {
                        return sprintf("%s/%s", $this->_docs, $this->_path);
                } elseif (strpos($target, '/') === false) {
                        return sprintf("%s/%s/%s", $this->_docs, $this->_path, $target);
                } else {
                        return sprintf("%s/%s", $this->_docs, $target);
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
                        'action' => '/^(stat|create|read|update|delete|rename|move|link|copy|list)$/',
                        'source' => '/^(secure-page|secure-view|secure-service|standard-page|standard-view|standard-service|router|directory|file)$/'
                ));

                switch ($request->getParam('action')) {
                        case 'list':
                                $iterator = new FilesIterator(parent::path());
                                $this->listing($iterator);
                                break;
                        case 'create':
                                $this->create($request->getParam('source'), $this->path($request->getParam('target')));
                                break;
                        default:
                                $request->removeFilter('source');
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
                switch ($source) {
                        case 'directory':
                                $this->mkdir($target);
                                break;
                        case 'file':
                                $this->touch($target);
                                break;
                        default:
                                $template = $this->template($source);
                                parent::create($template, $target);
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
                        'secure-page'      => 'files/secure/page.phpt',
                        'secure-view'      => 'files/secure/view.phpt',
                        'secure-service'   => 'files/secure/service.phpt',
                        'standard-page'    => 'files/standard/page.phpt',
                        'standard-view'    => 'files/standard/view.phpt',
                        'standard-service' => 'files/standard/service.phpt',
                        'router'           => 'files/standard/router.phpt'
                );

                return realpath("../templates/" . $sources[$source]);
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
                } else {
                        $this->send();
                }
        }

        /**
         * Create empty file.
         * @param string $target The target file.
         */
        private function touch($target)
        {
                if (!touch($target)) {
                        throw new RuntimeException(_("Failed create file"));
                } else {
                        $this->send();
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
                        'action' => '/^(stat|create|read|update|delete|move|link|copy|list|add|remove)$/',
                        'source' => '/^(sidebar|standard|topbar)$/'
                ));

                switch ($request->getParam('action')) {
                        case 'list':
                                $iterator = new MenusIterator(parent::path());
                                $this->listing($iterator);
                                break;
                        case 'create':
                                $this->create($request->getParam('source'), $this->path($request->getParam('target')));
                                break;
                        case 'add':
                                break;
                        case 'remove':
                                break;
                        default:
                                $request->removeFilter('source');
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
                $template = $this->template($source);
                parent::create($template, $target);
        }

        /**
         * Get template file.
         * @param string $source The template name.
         * @return string
         */
        private function template($source)
        {
                $sources = array(
                        'sidebar'  => 'menus/sidebar.menu',
                        'standard' => 'menus/standard.menu',
                        'topbar'   => 'menus/topbar.menu'
                );

                return realpath("../templates/" . $sources[$source]);
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
                        'action' => '/^(stat|create|read|update|delete|move|link|copy|list)$/',
                        'source' => '/^(content|headers|publish)$/'
                ));

                switch ($request->getParam('action')) {
                        case 'list':
                                $iterator = new ContextIterator(parent::path());
                                $this->listing($iterator);
                                break;
                        case 'create':
                                $this->create($request->getParam('source'), $this->path($request->getParam('target')));
                                break;
                        default:
                                $request->removeFilter('source');
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
                $template = $this->template($source);
                parent::create($template, $target);
        }

        /**
         * Get template file.
         * @param string $source The template name.
         * @return string
         */
        private function template($source)
        {
                $sources = array(
                        'content' => 'context/content.spec',
                        'headers' => 'context/headers.inc',
                        'publish' => 'context/publish.inc'
                );

                return realpath("../templates/" . $sources[$source]);
        }

}
