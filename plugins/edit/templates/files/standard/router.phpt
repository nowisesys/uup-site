<?php

@license@

// 
// Author: @author@
// Date:   @datetime@
// 

// 
// TODO: Set correct path for autoloader:
// 
require_once(realpath(__DIR__ . '/../../vendor/autoload.php'));

use UUP\Site\Request\Router;

// 
// See router class for methods to tweak suffix, namespace, extension and 
// directory is wanted.
// 
$router = new Router();
$router->handle();
