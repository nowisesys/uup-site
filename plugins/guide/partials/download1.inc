<?php

$downloads = new UUP\Web\Component\Container\Downloads();
$downloads->addLocation("stable");
$downloads->addLocation("testing");
$downloads->title = "Project1";
$downloads->render();
