<?php

use UUP\Site\Page\Web\StandardPage;

class DownloadPage extends StandardPage
{

        public function __construct()
        {
                parent::__construct("Download");
        }

        public function printContent()
        {
                include("partials/download.inc");
        }

}
