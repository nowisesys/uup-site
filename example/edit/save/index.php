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

/**
 * Content management handler.
 * 
 * This class is responsible for create, read, update and delete (CRUD) of
 * web content (files and folders). The response is JSON encoded except for
 * when reading files.
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
        private $_action;
        /**
         * The source file.
         * @var string 
         */
        private $_source;
        /**
         * The target directory.
         * @var string 
         */
        private $_path;

        public function __construct()
        {
                parent::__construct();

                $this->params->setFilter(array(
                        'action' => '/^(create|read|update|delete)$/'
                ));

                $this->_action = $this->params->getParam('action');
                $this->_source = $this->params->getParam('source');
                $this->_path = $this->params->getParam('path');

                if (!$this->_path) {
                        throw new RuntimeException(_("Required target directory parameter is missing"));
                }
                if (!$this->_action) {
                        throw new RuntimeException(_("Required action parameter (create, read, update or delete) is missing"));
                }
        }

        /**
         * Exception handler.
         * @param Exception $exception The exception to report.
         */
        public function onException($exception)
        {
                echo json_encode(array(
                        'status'  => 'failed',
                        'message' => $exception->getMessage(),
                        'code'    => $exception->getCode()
                ));
        }

        public function render()
        {
                
        }

}

$page = new IndexPage();
$page->render();
