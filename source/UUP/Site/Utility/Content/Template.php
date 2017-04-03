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

namespace UUP\Site\Utility\Content;

/**
 * Template file class.
 *
 * @author Anders Lövgren (QNET/BMC CompDept)
 * @package UUP
 * @subpackage Site
 */
class Template
{

        /**
         * The template file.
         * @var string 
         */
        private $_source;
        /**
         * The target file.
         * @var string 
         */
        private $_target;
        /**
         * The page author.
         * @var string 
         */
        private $_author;

        /**
         * Constructor.
         * @param string $source The template file.
         * @param string $target The target file.
         * @param string $author The page author.
         */
        public function __construct($source, $target, $author)
        {
                $this->_source = $source;
                $this->_target = $target;
                $this->_author = $author;
        }

        /**
         * Create the page.
         * 
         * @param string $name The class name
         * @throws Exception
         */
        public function create($name, $suffix = 'Page')
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
                        '@name@'     => sprintf("%s%s", ucfirst($name), $suffix),
                        '@title@'    => ucfirst($name)
                );

                $content = file_get_contents($this->source);
                $content = str_replace(array_keys($subst), $subst, $content);

                if (!file_put_contents($this->target, $content)) {
                        throw new Exception("Failed create target file");
                }
        }

}
