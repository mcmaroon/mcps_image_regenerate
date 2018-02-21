<?php

use Symfony\Component\Console\Command\Command;

class ImageManager extends ImageManagerCore
{

    private static $command;

    public final function setCommand(Command $command)
    {
        self::$command = $command;
    }

    public static function resize($sourceFile, $destinationFile, $destinationWidth = null, $destinationHeight = null, $fileType = 'jpg', $forceType = false, &$error = 0, &$targetWidth = null, &$targetHeight = null, $quality = 5, &$sourceWidth = null, &$sourceHeight = null)
    {
        self::$command->progressAdvance($sourceFile);
        return parent::resize($sourceFile, $destinationFile, $destinationWidth, $destinationHeight, $fileType, $forceType, $error, $targetWidth, $targetHeight, $quality, $sourceWidth, $sourceHeight);
    }
}
