<?php

$download = new BMCIT\Download("Project1");
$download->add("stable");
$download->add("testing");
$download->render();
