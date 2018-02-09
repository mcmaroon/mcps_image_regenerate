<?php
require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Console\Application;
use MCPS\ImageRegenerate\Command;

$application = new Application();
$application->add(new Command\ImageRegenerateCommand());
$application->run();
