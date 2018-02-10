<?php

use Symfony\Component\Console\Command\Command;

class AdminImagesController extends AdminImagesControllerCore
{

    private $command;

    public final function setCommand(Command $command)
    {
        $this->command = $command;
    }

    public function _regenerateThumbnails($type = 'all', $deleteOldImages = false)
    {
        parent::_regenerateThumbnails($type, $deleteOldImages);
        if (count($this->errors)) {
            $this->command->writeln(sprintf('Completed with (%s) errors', count($this->errors)), 'error');
            foreach ($this->errors as $errorMessage) {
                $this->command->writeln($errorMessage, 'error');
            }
        } else {
            $this->command->writeln('The thumbnails were successfully regenerated.', 'info');
        }
    }

    protected function _regenerateNewImages($dir, $type, $productsImages = false)
    {
        $this->command->write('Processing: ', 'info');
        $this->command->writeln($dir);
        parent::_regenerateNewImages($dir, $type, $productsImages);
    }
}
