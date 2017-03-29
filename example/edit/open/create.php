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

use UUP\Site\Page\Web\Security\SecurePage;

class Mapper
{

        public $file;
        public $sect;
        public $type;
        public $secure;

        public function __construct($type)
        {
                $parts = explode('-', $type);

                if ($parts[0] == 'dir') {
                        $this->file = false;
                } else {
                        $this->file = true;
                }

                if ($this->file) {
                        switch ($parts[0]) {
                                case 'content':
                                        $this->sect = $parts[0];
                                        $this->type = $parts[2];
                                        $this->secure = $parts[1] == 'secure';
                                        break;
                                case 'context':
                                        $this->sect = $parts[0];
                                        $this->type = $parts[1];
                                        $this->secure = false;
                                        break;
                                case 'menus':
                                        $this->sect = $parts[0];
                                        $this->type = $parts[1];
                                        $this->secure = false;
                                        break;
                        }
                }
        }

}

class Creator
{

        public $source;
        public $target;
        public $author;

        public function generate($name)
        {
                if (!file_exists($this->source)) {
                        throw new Exception("The source file don't exist");
                }
                if (file_exists($this->target)) {
                        throw new Exception("The target file already exist");
                }

                $subst = array(
                        '@year@'     => date('Y'),
                        '@author@'   => $this->author,
                        '@datetime@' => strftime("%x %X"),
                        '@name@'     => sprintf("%sPage", ucfirst($name)),
                        '@title@'    => ucfirst($name)
                );

                $content = file_get_contents($this->source);
                $content = str_replace(array_keys($subst), $subst, $content);

                if (!file_put_contents($this->target, $content)) {
                        throw new Exception("Failed create target file");
                }
        }

}

/**
 * Content create handler.
 * 
 * The type parameter is i.e. 'content-secure-page' or 'menus-standard' and is 
 * mapped by into the new template file directory. 
 * 
 * Example POST request: 
 * 
 * o) ?dir=dir1&type=menus-standard
 * o) ?dir=dir1&type=content-secure-page&name=about
 *
 * @author Anders Lövgren (QNET/BMC CompDept)
 * @package UUP
 * @subpackage Site
 */
class CreatePage extends SecurePage
{

        /**
         * Parent directory.
         * @var string 
         */
        private $_dir;
        /**
         * The content type.
         * @var string 
         */
        private $_type;
        /**
         * An optional name.
         * @var string 
         */
        private $_name;
        /**
         * The new file or directory.
         * @var string 
         */
        private $_file;
        /**
         * The template directory.
         * @var string 
         */
        private $_from;

        public function __construct()
        {
                parent::__construct(__CLASS__, null);

                if (!in_array($this->session->user, $this->config->edit['user'])) {
                        throw new Exception('Caller is not an page/site editor');
                }

                $this->_dir = filter_input(INPUT_POST, 'dir', FILTER_SANITIZE_STRING);
                $this->_type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);

                if (!($this->_dir)) {
                        throw new Exception('Required parameter dir is empty');
                }
                if (!($this->_type)) {
                        throw new Exception('Required parameter type is empty');
                }

                $this->_name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
                $this->_from = realpath(__DIR__ . '/../templates');
        }

        public function process()
        {
                if (!file_exists($this->_dir)) {
                        throw new Exception('The parent directory is missing');
                }
                if (!is_dir($this->_dir)) {
                        throw new Exception('Parent is not a directory');
                }
                if (!is_writable($this->_dir)) {
                        throw new Exception('The parent directory is not writable');
                }

                $mapper = new Mapper($this->_type);

                if ($mapper->file) {
                        $creator = new Creator();

                        switch ($mapper->sect) {
                                case 'content':
                                        if ($mapper->secure) {
                                                $creator->source = sprintf("%s/content/secure/%s.phpt", $this->_from, $mapper->type);
                                                $creator->target = sprintf("%s/%s.php", $this->_dir, $this->_name);
                                        } else {
                                                $creator->source = sprintf("%s/content/standard/%s.phpt", $this->_from, $mapper->type);
                                                $creator->target = sprintf("%s/%s.php", $this->_dir, $this->_name);
                                        }
                                        break;
                                case 'context':
                                        if ($mapper->type == 'content') {
                                                $creator->source = sprintf("%s/context/content.spec", $this->_from);
                                                $creator->target = sprintf("%s/content.spec", $this->_dir);
                                        } else {
                                                $creator->source = sprintf("%s/context/%s.inc", $this->_from, $mapper->type);
                                                $creator->target = sprintf("%s/%s.inc", $this->_dir, $mapper->type);
                                        }
                                        break;
                                case 'menus':
                                        $creator->source = sprintf("%s/menus/%s.menu", $this->_from, $mapper->type);
                                        $creator->target = sprintf("%s/%s.menu", $this->_dir, $mapper->type);
                                        break;
                        }

                        $creator->author = $this->session->user;
                        $creator->generate($this->_name);
                } else {
                        $this->_file = sprintf("%s/%s", $this->_dir, $this->_name);
                        if (!mkdir($this->_file)) {
                                throw new Exception("Failed create directory");
                        }
                }
        }

        public function printContent()
        {
                echo json_encode(array(
                        'status' => 'success',
                        'target' => $this->_file
                ));
        }

}

try {
        $page = new CreatePage();
        $page->process();
        $page->render();
} catch (Exception $exception) {
        echo json_encode(array(
                'status' => 'failed',
                'reason' => $exception->getMessage()
        ));
}
