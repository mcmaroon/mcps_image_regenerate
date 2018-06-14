<?php
namespace MCPS\ImageRegenerate\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

class ImageRegenerateCommand extends Command
{

    private $output;
    private $progress;

    protected function configure()
    {
        $this
            ->setName('image-regenerate')
            ->setDescription('Regenerates thumbnails for all existing images')
            ->setDefinition(array(
                new InputOption(
                    'dir', null, InputOption::VALUE_REQUIRED, 'Main directory of the prestashop project', getcwd()
                ),
                new InputOption(
                    'type', null, InputOption::VALUE_OPTIONAL, 'Name for the image type', 'all'
                ),
                new InputOption(
                    'format', null, InputOption::VALUE_OPTIONAL, 'Name for the image format', 'all'
                ),
                new InputOption(
                    'erase', null, InputOption::VALUE_NONE, 'Erase previous images'
                ),
        ));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $dir = $input->getOption('dir');
        $type = $input->getOption('type');
        $format = $input->getOption('format');
        $erase = (boolean) $input->getOption('erase');

        if (!file_exists($dir)) {
            throw new \InvalidArgumentException(sprintf('Invalid dir. %s not exists', $dir));
        }

        $configPath = $dir . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.inc.php';
        if (!file_exists($configPath)) {
            throw new \RuntimeException(sprintf('Cannot find the %s file in the given directory %s', $configPath, $dir));
        }

        define('_PS_ADMIN_DIR_', $dir . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . 'admin');
        require_once($configPath);

        $this->write('PROJECT DIRECTORY ', 'info');
        $this->write($dir);
        $this->write(' PS VERSION ', 'info');
        $this->writeln(_PS_VERSION_);

        $this->write('CONFIGURATION: type: ', 'info');
        $this->write($type);
        $this->write(' format: ', 'info');
        $this->write($format);
        $this->write(' erase: ', 'info');
        $this->writeln(($erase ? 'true' : 'false'));

        $psCipherAlgorithm = \Configuration::get('PS_CIPHER_ALGORITHM');
        if ($psCipherAlgorithm) {
            $this->write('PS_CIPHER_ALGORITHM: ', 'comment');
            $this->write('true');
        }

        require_once(__DIR__ . '/../Override' . DIRECTORY_SEPARATOR . 'AdminImagesController.php');
        require_once(__DIR__ . '/../Override' . DIRECTORY_SEPARATOR . 'ImageManager.php');
        $_GET['format_' . $type] = $this->convertFormatToDbValue($type, $format); // AdminImagesControllerCore Line 657


        $r = new \ImageManager();
        $r->setCommand($this);

        $aic = new \AdminImagesController();
        $aic->setCommand($this);
        $aic->_regenerateThumbnails($type, $erase);
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

    public final function progressStart()
    {
        $this->progress = new ProgressBar($this->output);
        $this->progress->setBarWidth(10);
        $this->progress->setFormat('%current% [%bar%] <info>%message:3s%</info>');
        $this->progress->setMessage('');
    }

    public final function progressAdvance($sourceFile)
    {
        //$this->progress->advance();
        $this->progress->setMessage('File:' . $sourceFile);
    }

    public final function progressEnd()
    {
        $this->progress->setMessage('');
        $this->progress->finish();
    }
}
