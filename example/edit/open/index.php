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
 * Open page editor.
 *
 * @author Anders Lövgren (QNET/BMC CompDept)
 * @package UUP
 * @subpackage Site
 */
class IndexPage extends SecurePage
{

        public function __construct()
        {
                parent::__construct(_("Site and page editor"));
                
                if (!in_array($this->session->user, $this->config->edit['user'])) {
                        throw new Exception('Caller is not an page/site editor');
                }
        }

        public function printContent()
        {
                require_once('editor.phtml');
                require_once('listing.phtml');
        }

}

$page = new IndexPage();
$page->render();
