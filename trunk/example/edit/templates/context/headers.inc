<?php

// 
// Custom HTTP headers. Typical used for providing <meta> tags.
// 
// Author: @author@
// Date:   @datetime@
// 

return array(
        'meta' => array(
                array(
                        'name'    => 'description',
                        'content' => 'Site and application template system'
                ),
                array(
                        'name'    => 'author',
                        'content' => 'Your name'
                ),
                array(
                        'name'    => 'keywords',
                        'content' => 'php,template,router'
                ),
                array(
                        'http-equiv' => 'Content-Type',
                        'content'    => 'text/html;charset=utf-8'
                )
        )
);
