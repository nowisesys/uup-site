<?php

// 
// Uses the standard page class.
// 

use UUP\Site\Page\Web\StandardPage;

class IndexPage extends StandardPage
{

        public function __construct()
        {
                parent::__construct("Start Page");
        }

        public function printContent()
        {
                include("fragment/index.inc");
                include("fragment/standard.inc");
                include("fragment/common.inc");
        }

}
