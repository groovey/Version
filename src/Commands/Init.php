<?php

namespace Groovey\Version\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Groovey\Version\Version;
use Groovey\Support\Output;

class Init extends Command
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
            ->setName('version:init')
            ->setDescription('Setup the versions directory and database')
        ;
    }

    private function init()
    {
        $app = $this->app;

        $query = '
                CREATE TABLE IF NOT EXISTS `versions` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `version` char(7) COLLATE utf8_unicode_ci NOT NULL UNIQUE,
                  `changelog` text COLLATE utf8_unicode_ci NOT NULL,
                  `created_at` datetime NOT NULL,
                  `updated_at` datetime NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
            ';

        return $app['db']::statement($query);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->init();

        $output = Output::style($output);
        $folder = getcwd().'/vendor/groovey/framework/database/versions';

        if (false === @mkdir($folder, 0755, true) && !file_exists($folder)) {
            $output->writeln('<error>Unable to create folder. Check file permissions.</error>');

            return;
        }

        if (file_exists($folder) && is_dir($folder)) {
            $output->writeln('<highlight>Sucessfully created a versions folder.</highlight>');
            $output->writeln('<info>Place all your version files in:</info>');
            $output->writeln("<comment>$folder</comment>");
        }
    }
}
