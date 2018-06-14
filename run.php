<?php
require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Application;
use MCPS\ImageRegenerate\Command\ImageRegenerateCommand;

set_time_limit(0);

$input = new ArgvInput();
$command = new ImageRegenerateCommand();
$application = new Application();
$application->add($command);
$application->setDefaultCommand($command->getName());
$application->run($input);
