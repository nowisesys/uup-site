<?php

/*
 * Copyright (C) 2017-2018 Anders Lövgren (Computing Department at BMC, Uppsala University).
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

require_once('handler.php');

use UUP\Site\Page\Service\SecureService;
use UUP\Site\Utility\Content\Template;

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
 * @author Anders Lövgren (Nowise Systems/BMC-IT, Uppsala University)
 * @package UUP
 * @subpackage Site
 */
class IndexPage extends SecureService
{

        /**
         * Constructor.
         * @throws RuntimeException
         */
        public function __construct()
        {
                parent::__construct();

                if (!$this->isEditor($this->session->user)) {
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
         * Check if user is editor.
         * 
         * @param string $user The username.
         * @return bool
         * @throws RuntimeException
         */
        private function isEditor($user)
        {
                $source = $this->config->edit['user'];

                if (is_array($source)) {
                        return in_array($user, $source);
                }
                if (is_callable($source)) {
                        return $source($user);
                }
                if (is_bool($source)) {
                        return $source === true;
                }

                throw new RuntimeException('Unsupported source for editor validation');
        }

        /**
         * Exception handler.
         * @param Exception $exception The exception to report.
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
                                $handler = new FilesHandler($this->config->docs, $this->params->getParam('path'));
                                $handler->setService($this);
                                $handler->process($this->params);
                                break;
                        case 'menus':
                                $handler = new MenusHandler($this->config->docs, $this->params->getParam('path'));
                                $handler->setService($this);
                                $handler->process($this->params);
                                break;
                        case 'context':
                                $handler = new ContextHandler($this->config->docs, $this->params->getParam('path'));
                                $handler->setService($this);
                                $handler->process($this->params);
                                break;
                }
        }

        /**
         * Get template object.
         * @return Template
         */
        public function getTemplate()
        {
                $template = new Template($this->config->license);
                $template->license->path = realpath('../templates/license');
                $template->author = $this->session->user;
                return $template;
        }

}
