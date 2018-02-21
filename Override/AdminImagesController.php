<?php

use Symfony\Component\Console\Command\Command;

class AdminImagesController extends AdminImagesControllerCore
{

    private static $command;

    public final function setCommand(Command $command)
    {
        self::$command = $command;
    }

    public function _regenerateThumbnails($type = 'all', $deleteOldImages = false)
    {
        parent::_regenerateThumbnails($type, $deleteOldImages);
        if (count($this->errors)) {
            self::$command->writeln('');
            self::$command->writeln(sprintf('Completed with (%s) errors', count($this->errors)), 'error');
            foreach ($this->errors as $errorMessage) {
                self::$command->writeln($errorMessage, 'error');
            }
        } else {
            self::$command->writeln('');
            self::$command->writeln('The thumbnails were successfully regenerated.', 'info');
        }
    }

    protected function _regenerateNewImages($dir, $type, $productsImages = false)
    {
        self::$command->progressStart();
        self::$command->writeln('');
        self::$command->write('Processing: ', 'info');
        self::$command->writeln($dir);
        parent::_regenerateNewImages($dir, $type, $productsImages);
        self::$command->progressEnd();
    }
}
