<?php

/*
 * Copyright (C) 2017 Anders Lövgren (Nowise Systems/BMC-IT, Uppsala University).
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

namespace UUP\Site\Request;

use UUP\Site\Page\Web\ErrorPage;

/**
 * The error handler object.
 * 
 * @property-read array $handlers The array of handlers.
 *
 * @author Anders Lövgren (Nowise Systems/BMC-IT, Uppsala University)
 * @package UUP
 * @subpackage Site
 */
class Error
{

        /**
         * The URI handlers.
         * @var array 
         */
        private $_handlers;

        /**
         * Constructor.
         * @param array $handlers The URI handlers.
         */
        public function __construct($handlers = array())
        {
                $this->_handlers = $handlers;
        }

        public function __get($name)
        {
                if ($name == 'handlers') {
                        return $this->_handlers;
                }
        }

        /**
         * Add an URI handler.
         * 
         * The handler is either an script or an callable. A default
         * handler can be installed using '*' as $uri.
         * 
         * @param string $uri The request URI.
         * @param string|callable $handler The exception handler.
         */
        public function addHandler($uri, $handler)
        {
                if (!isset($this->_handlers[$uri])) {
                        $this->_handlers[$uri] = $handler;
                }
        }

        /**
         * Set an URI handler.
         * 
         * The handler is either an script or an callable. A default
         * handler can be installed using '*' as $uri.
         * 
         * @param string $uri The request URI.
         * @param string|callable $handler The exception handler.
         */
        public function setHandler($uri, $handler)
        {
                if (!isset($this->_handlers[$uri])) {
                        $this->_handlers[$uri] = $handler;
                }
        }

        /**
         * Set array of URI handlers.
         * @param array $handlers The URI handlers.
         */
        public function setHandlers($handlers)
        {
                $this->_handlers = $handlers;
        }

        /**
         * Handle exception for URI.
         * 
         * Match request against registered URI handlers. The default handler
         * using '*' as URI is matched last. If no handler matches, then die
         * is caller on the script.
         * 
         * @param string $request The request URI.
         * @param Exception $exception The exception to handle.
         */
        public function handle($request, $exception)
        {
                // 
                // Try to match sub path in URI:
                // 
                foreach ($this->_handlers as $uri => $handler) {
                        if (strncmp($request, $uri, strlen($uri) != 0)) {
                                continue;
                        }
                        if ($this->process($handler, $request, $exception)) {
                                return true;
                        }
                }

                // 
                // Check for default handler:
                // 
                if (array_key_exists('*', $this->_handlers)) {
                        if ($this->process($this->_handlers['*'], $request, $exception)) {
                                return true;
                        }
                }

                // 
                // No accepted handler:
                // 
                die($exception->getMessage());
        }

        /**
         * Process this exception handler.
         * 
         * @param mixed $handler The exception handler.
         * @param string $request The request URI.
         * @param Exception $exception The exception to handle.
         * @return bool
         */
        private function process($handler, $request, $exception)
        {
                if (is_bool($handler)) {
                        ErrorPage::show($exception);
                }
                if (is_callable($handler)) {
                        call_user_func($handler, $request, $exception);
                        return true;
                }
                if (is_string($handler) && file_exists($handler)) {
                        require_once($handler);
                        return true;
                }

                return false;
        }

}
