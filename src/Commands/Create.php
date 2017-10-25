<?php

namespace Groovey\Version\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Groovey\Version\Version;
use Groovey\Support\Output;

class Create extends Command
{
    private $app;

    public function __construct($app)
    {
        parent::__construct();

        $this->app = $app;
    }

    protected function configure()
    {
        $this
            ->setName('version:create')
            ->setDescription('Creates a .yml version file')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app       = $this->app;
        $output    = Output::style($output);
        $directory = Version::getDirectory();
        $data      = Version::getTemplate();
        $version   = Version::getNextVersion($app);

        $helper    = $this->getHelper('question');

        $filename  = $version.'.yml';
        $question  = new ConfirmationQuestion('<question>Are you sure you want to proceed? (Y/n):</question> ', false);

        $output->writeln('<highlight>Creating version file:</highlight>');
        $output->writeln("<info> - $filename</info>");

        if (!$helper->ask($input, $output, $question)) {
            return;
        }

        if (file_exists($directory.'/'.$filename)) {
            $output->writeln("<error>The version file already $filename exists.</error>");

            return;
        }

        file_put_contents($directory.'/'.$filename, $data);

        $text = '<info>Sucessfully created version file.</info>';
        $output->writeln($text);
    }
}
