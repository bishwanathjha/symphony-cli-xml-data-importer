#!/usr/bin/env php
<?php
// application.php

require __DIR__.'/vendor/autoload.php';

use App\Command\XMLReaderCommand;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Console\Application;

$application = new Application();

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env');

// ... register commands
$application->add(new XMLReaderCommand());

$application->run();

// php bin/console app:xml-reader
