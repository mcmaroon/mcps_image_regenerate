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
                    'format', 'f', InputOption::VALUE_OPTIONAL, 'Name for the image format', 'all'
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
        $format = $input->getOption('format');
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

        $this->write('PROJECT DIRECTORY ', 'info');
        $this->write($projectdir);
        $this->write(' PS VERSION ', 'info');
        $this->writeln(_PS_VERSION_);

        $this->write('CONFIGURATION: type: ', 'info');
        $this->write($type);
        $this->write(' format: ', 'info');
        $this->write($format);
        $this->write(' deleteOldImages: ', 'info');
        $this->writeln($deleteOldImages);

        require_once('Controller' . DIRECTORY_SEPARATOR . 'AdminImagesController.php');
        $_GET['format_' . $type] = $this->convertFormatToDbValue($type, $format); // AdminImagesControllerCore Line 657

        $aic = new \AdminImagesController();
        $aic->setCommand($this);
        $aic->_regenerateThumbnails($type, $deleteOldImages);
    }

    protected function convertFormatToDbValue($type, $inputformat)
    {
        if (class_exists('ImageType') && $type != 'all') {
            $formats = \ImageType::getImagesTypes($type);
            foreach ($formats as $format) {
                if ($format['name'] === $inputformat) {
                    return $format['id_image_type'];
                }
            }
        }

        return 'all';
    }

    protected function writeStyle($string, $style)
    {
        if (strlen($style)) {
            $string = '<' . $style . '>' . $string . '</' . $style . '>';
        }

        return $string;
    }

    public final function write($string, $style = '')
    {
        $this->output->write($this->writeStyle($string, $style));
    }

    public final function writeln($string, $style = '')
    {
        $this->output->writeln($this->writeStyle($string, $style));
    }
}
