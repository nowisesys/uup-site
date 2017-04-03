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
 * The menus section AJAX service.
 *
 * The AJAX method API: 
 * ---------------------
 * o) action=create&source=template&target=file                 // Create new menu.
 * o) action={read|update|delete}&target=file                   // Read, update or delete file.
 * o) action={rename|move|link}&source=from&target=dest         // Rename or move a file.
 * 
 * @author Anders Lövgren (QNET/BMC CompDept)
 * @package UUP
 * @subpackage Site
 */
class MenusPage extends IndexPage
{

        public function __construct()
        {
                parent::__construct();

                $this->params->setFilter(array(
                        'action' => '/^(create|read|update|delete|add|remove)$/',
                        'source' => '/^(sidebar|standard|topbar)$/'
                ));

                $this->_action = $this->params->getParam('action');
        }

        public function render()
        {
                switch ($this->_action) {
                        case 'add':
                                $this->add($this->_target, $this->_source, $this->_name);
                                break;
                        case 'remove':
                                $this->remove($this->_target, $this->_source);
                                break;
                        default:
                                parent::render();
                                break;
                }
        }

        /**
         * Add file to menu.
         * @param string $menu The target menu.
         * @param string $file The filename.
         * @param string $name The link name.
         */
        private function add($menu, $file, $name)
        {
                // TODO implement
        }

        /**
         * Remove file from menu.
         * @param string $menu The target menu.
         * @param string $file The filename.
         */
        private function remove($menu, $file)
        {
                // TODO implement                
        }

}
