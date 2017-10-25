<?php

namespace Groovey\Version\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Groovey\Version\Version;
use Groovey\Support\Output;

class Status extends Command
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
            ->setName('version:status')
            ->setDescription('List all the versions file that have not been migrated')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output = Output::style($output);

        $files = [];
        foreach (Version::getUnMigratedFiles($this->app) as $file) {
            $files[] = [$file];
        }

        if (!$files) {
            $output->writeln('<highlight>Nothing to migrate.</highlight>');

            return;
        }

        $table = new Table($output);
        $table
            ->setHeaders(['Unmigrated YML'])
            ->setRows($files)
        ;
        $table->render();
    }
}
