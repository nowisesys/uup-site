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

/**
 * The files section AJAX service.
 * 
 * The AJAX method API: 
 * ---------------------
 * o) action=create&source=template&target=file                 // Create new file.
 * o) action={read|update|delete}&target=file                   // Read, update or delete file.
 * o) action={rename|move|link}&source=from&target=dest         // Rename or move a file.
 * 
 * The create, delete, rename, move and link actions also applies to directories.
 * 
 * @author Anders Lövgren (QNET/BMC CompDept)
 * @package UUP
 * @subpackage Site
 */
class FilesPage extends IndexPage
{

        public function __construct()
        {
                parent::__construct();

                $this->params->setFilter(array(
                        'action' => '/^(create|read|update|delete|rename|move|link)$/'
                ));

                $this->_action = $this->params->getParam('action');
        }

        public function render()
        {
                switch ($this->_action) {
                        case 'create':
                                parent::create($this->_target, $this->_source);
                                break;
                        case 'read':
                                parent::read($this->_target);
                                break;
                        case 'update':
                                parent::update($this->_target);
                                break;
                        case 'delete':
                                parent::delete($this->_target);
                                break;
                        case 'rename':
                                parent::rename($this->_source, $this->_target);
                                break;
                        case 'move':
                                parent::move($this->_source, $this->_target);
                                break;
                        case 'link':
                                parent::link($this->_source, $this->_target);
                                break;
                }
        }

}
