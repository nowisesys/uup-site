<?php

$downloads = new UUP\Web\Component\Container\Downloads();
$downloads->addLocation("partials", "Partial files", array("inc"));
$downloads->title = "Download";
$downloads->info = "From here you can download all code fragment files used by the guide.";
$downloads->render();
