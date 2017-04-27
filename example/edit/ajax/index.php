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
require_once('handler.php');

use UUP\Site\Page\Service\SecureService;

/**
 * The site edit AJAX service.
 * 
 * The AJAX method API:
 * ---------------------
 * o) handler={file|menu|context}&path=subdir
 * 
 * Each handler defines its own sub API. The path parameter is mandatory and should be 
 * relative to project root.
 * 
 * @author Anders Lövgren (QNET/BMC CompDept)
 * @package UUP
 * @subpackage Site
 */
class IndexPage extends SecureService
{

        public function __construct()
        {
                parent::__construct();

                if (!in_array($this->session->user, $this->config->edit['user'])) {
                        throw new RuntimeException('Caller is not an page/site editor');
                }

                $this->params->setFilter(array(
                        'handler' => '/^(files|menus|context)$/'
                ));

                if (!$this->params->hasParam('handler')) {
                        throw new RuntimeException(_("Required handler parameter is missing"));
                }
                if (!$this->params->hasParam('path')) {
                        throw new RuntimeException(_("Required target directory parameter is missing"));
                }
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
                if (($path = $this->params->getParam('path'))) {
                        if ($path[0] == '/') {
                                throw new RuntimeException(_("Absolute pathes is not allowed"));
                        }
                        if (strstr($path, '..')) {
                                throw new RuntimeException(_("Directory navigation is not allowed"));
                        }
                }

                switch ($this->params->getParam('handler')) {
                        case 'files':
                                $handler = new FilesHandler($this->path());
                                $handler->process($this->params);
                                break;
                        case 'menus':
                                $handler = new MenusHandler($this->path());
                                $handler->process($this->params);
                                break;
                        case 'context':
                                $handler = new ContextHandler($this->path());
                                $handler->process($this->params);
                                break;
                }
        }

        /**
         * Get absolute path.
         * @return string
         */
        private function path()
        {
                return realpath($this->config->proj . '/' . $this->params->getParam('path'));
        }

}

$page = new IndexPage();
$page->render();
