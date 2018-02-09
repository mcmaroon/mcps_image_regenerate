<?php
namespace MCPS\ImageRegenerate\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

class ImageRegenerateCommand extends Command
{

    private $output;

    protected function configure()
    {
        $this
            ->setName('mcps:image-regenerate')
            ->setDescription('Regenerate Prestashop Images')
            ->setDefinition(array(
                new InputArgument(
                    'projectdir', InputArgument::OPTIONAL, 'The main project directory', getcwd()
                ),
                new InputOption(
                    'type', 't', InputOption::VALUE_OPTIONAL, 'Name for the image type', 'all'
                ),
                new InputOption(
                    'deleteOldImages', 'd', InputOption::VALUE_NONE, 'Erase previous images'
                ),
        ));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $projectdir = $input->getArgument('projectdir');
        $type = $input->getOption('type');
        $deleteOldImages = $input->getOption('deleteOldImages');

        if (!file_exists($projectdir)) {
            throw new \InvalidArgumentException(sprintf('Invalid projectdir. %s not exists', $projectdir));
        }

        $configPath = $projectdir . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.inc.php';
        if (!file_exists($configPath)) {
            throw new \RuntimeException(sprintf('Prestahop %s not exists', $configPath));
        }

        define('_PS_ADMIN_DIR_', $projectdir . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . 'admin');
        require_once($configPath);

        $this->output(sprintf('PS VERSION: %s', _PS_VERSION_), 'info');

        require_once('Controller' . DIRECTORY_SEPARATOR . 'AdminImagesController.php');
        $aic = new \AdminImagesController();
        $aic->setCommand($this);
        $aic->_regenerateThumbnails($type, $deleteOldImages);
    }

    public final function output($string, $style = '')
    {
        if (strlen($style)) {
            $string = '<' . $style . '>' . $string . '</' . $style . '>';
        }
        $this->output->writeln($string);
    }
}
