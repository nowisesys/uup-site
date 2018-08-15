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

use UUP\Site\Page\Web\Security\SecurePage;

/**
 * Open page editor.
 *
 * @author Anders Lövgren (Nowise Systems/BMC-IT, Uppsala University)
 * @package UUP
 * @subpackage Site
 */
class IndexPage extends SecurePage
{

        /**
         * The page path.
         * @var string 
         */
        private $_path;

        /**
         * Constructor.
         * @throws RuntimeException
         */
        public function __construct()
        {
                parent::__construct(_("Site and page editor"));
                $this->_path = $this->params->getParam('path');

                if (!$this->isEditor($this->session->user)) {
                        throw new RuntimeException('Caller is not an page/site editor');
                }
                if (!$this->_path) {
                        throw new RuntimeException('Missing required parameter path');
                }
        }

        public function printContent()
        {
                require_once('index.phtml');
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

}
