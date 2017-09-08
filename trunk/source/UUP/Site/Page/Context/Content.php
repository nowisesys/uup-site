<?php

/*
 * Copyright (C) 2016-2017 Anders Lövgren (Computing Department at BMC, Uppsala University).
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

namespace UUP\Site\Page\Context;

/**
 * Support for content specification.
 * 
 * Content specification is usually utilized by placing a file named content.spec
 * in the same directory as the page returning an array having the mandatory name
 * and desc keys.
 * 
 * Either one of the image or video key should be used (if at all). Links are an
 * complete URL or a path relative to the content spec file. The possible array
 * keys are:
 * 
 * <code>
 * array(
 *      'name'  => string       // mandatory
 *      'desc'  => string       // mandatory
 *      'info'  => string       // optional
 *      'tags'  => array        // optional
 *      'image' => string       // optional
 *      'video' => string       // optional
 * )
 * </code>
 * 
 * Content specification can be enabled at top level by either define the array
 * content in defaults.site or supplying an file inside the templates directory.
 * 
 * @property-read string $name The content name.
 * @property-read string $desc The content description text.
 * @property-read string $info Short text used as introduction.
 * @property-read array $tags Meta data tags.
 * @property-read string $image An optional image (relative or URL).
 * @property-read string $video An optional video (relative or URL).
 * 
 * @author Anders Lövgren (QNET/BMC CompDept)
 * @package UUP
 * @subpackage Site
 */
class Content extends \ArrayObject
{

        /**
         * Constructor.
         * @param string $template The template directory.
         * @param array|boolean $content Optional content specification.
         * @return type
         */
        public function __construct($template, $content = false)
        {
                if ($content != false) {
                        if (file_exists("content.spec")) {
                                parent::__construct(include("content.spec"));
                        } elseif (file_exists(sprintf("%s/content.spec", $template))) {
                                parent::__construct(include(sprintf("%s/content.spec", $template)));
                        } elseif (is_array($content)) {
                                parent::__construct($content);
                        }
                }
        }

        public function __get($name)
        {
                if (isset($this[$name])) {
                        return $this[$name];
                }
        }

}
