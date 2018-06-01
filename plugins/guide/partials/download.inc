<?php

$download = new BMCIT\Download("Partial files");
$download->add("partials", null, "inc", "Code fragment files used in the site guide");
$download->render();
