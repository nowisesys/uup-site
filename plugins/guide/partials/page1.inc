<?php

use UUP\Site\Page\Web\StandardPage;

// 
// Use happy-new-year as request URI for this controller.
// 
class HappyNewYearPage extends StandardPage
{

        public function __construct()
        {
                parent::__construct("Happy new year!");         // Set page title
        }

        public function printContent()
        {
                printf("Happy new year %d, %s", date('Y'), $this->session->user);
        }

}
