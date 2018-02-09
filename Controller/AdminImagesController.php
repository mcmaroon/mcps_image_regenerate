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
            $this->command->output(sprintf('Completed with errors (%s)', count($this->errors)), 'error');
            foreach ($this->errors as $errorMessage) {
                $this->command->output($errorMessage, 'error');
            }
        }
    }

    protected function _regenerateNewImages($dir, $type, $productsImages = false)
    {
        $this->command->output(sprintf('Directory: %s', $dir));
        parent::_regenerateNewImages($dir, $type, $productsImages);
    }
}
