<?php

use mail2print\Commands\Mail2Print;
use Symfony\Component\Console\Application;

$autoLoader = require_once __DIR__ . '/../vendor/autoload.php';

$command = new Mail2Print();

$application = new Application();
$application->add($command);
$application->run();


