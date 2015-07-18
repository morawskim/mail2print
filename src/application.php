<?php

use mail2print\Commands\Mail2Print;
use Monolog\Logger;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputOption;

define('MAIL2PRINT_VERSION', '0.1.0');

$autoLoader = require_once __DIR__ . '/../vendor/autoload.php';

$application = new Application('mail2print', MAIL2PRINT_VERSION);
$application->getDefinition()->addOption(
    new InputOption('log', 'l', InputOption::VALUE_OPTIONAL,
        'The path to log file', '/var/log/mail2print.log'));

$logger = new Logger('mail2print');
$logger->pushHandler(new \Monolog\Handler\StreamHandler('php://stderr', Logger::ERROR));
\Monolog\ErrorHandler::register($logger);

$command = new Mail2Print();
$command->setLogger($logger);

$application->setCatchExceptions(false);
$application->add($command);
$application->run();
