<?php

require_once(realpath(__DIR__ . '/../../vendor/autoload.php'));

use UUP\Site\Page\Web\Migration\TransitionalPage;

// 
// Drop in for missing include file.
// 

if (!function_exists('load_ui_template')) {

        function load_ui_template($template)
        {
                if (!defined('UUP_SITE_DIRECT_RENDERING')) {
                        $page = new TransitionalPage(filter_input(INPUT_SERVER, 'SCRIPT_FILENAME'));
                        $page->setTemplate($template);
                        $page->render();
                }
        }

}
