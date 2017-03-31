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

use UUP\Site\Page\Web\Security\SecurePage;

/**
 * Save edited page.
 *
 * @author Anders Lövgren (QNET/BMC CompDept)
 * @package UUP
 * @subpackage Site
 */
class IndexPage extends SecurePage
{

        /**
         * The page content.
         * @var string 
         */
        private $_html;
        /**
         * The target page (URI).
         * @var string 
         */
        private $_page;
        /**
         * The target file.
         * @var string 
         */
        private $_file;

        public function __construct()
        {
                parent::__construct(__CLASS__, null);

                if (!in_array($this->session->user, $this->config->edit['user'])) {
                        throw new Exception('Caller is not an page/site editor');
                }
                if (!($this->_html = filter_input(INPUT_POST, 'html', FILTER_SANITIZE_STRING))) {
                        throw new Exception('Required parameter html is empty');
                }
                if (!($this->_page = filter_input(INPUT_POST, 'page', FILTER_SANITIZE_STRING))) {
                        throw new Exception('Required parameter page is empty');
                }
                if (!($this->_file = filter_input(INPUT_POST, 'file', FILTER_SANITIZE_STRING))) {
                        throw new Exception('Required parameter file is empty');
                }
                if (!file_exists($this->_file)) {
                        throw new Exception("Target file do not exist");
                }

                error_log(print_r($this, true));
        }

        public function printContent()
        {
                error_log(print_r($_POST, true));

                echo json_encode(array('status' => 'success'));
        }

}

try {
        $page = new IndexPage();
        $page->render();
} catch (Exception $exception) {
        echo json_encode(array(
                'status' => 'failed',
                'reason' => $exception->getMessage()
        ));
}
