<?php

namespace Groovey\Version\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Yaml\Parser;
use Groovey\Version\Version;
use Groovey\Support\Output;

class Down extends Command
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
            ->setName('version:down')
            ->setDescription('Reverse the version')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app      = $this->app;
        $output   = Output::style($output);
        $yaml     = new Parser();
        $dir      = Version::getDirectory();
        $helper   = $this->getHelper('question');
        $question = new ConfirmationQuestion('<question>Are you sure you want to proceed? (y/N):</question> ', false);

        $records = $app['db']->table('versions')->orderBy('version', 'DESC')->take(1)->get();

        if (count($records) == 0) {
            $output->writeln('<error>Nothing to downgrade.</error>');

            return;
        }

        if (count($records) == 0) {
            $output->writeln('<error>Nothing to downgrade.</error>');

            return;
        }

        $output->writeln('<highlight>Version will downgrade to the following files:</highlight>');

        foreach ($records as $record) {
            $output->writeln("<info>- {$record->version}.yml </info>");
        }

        if (!$helper->ask($input, $output, $question)) {
            return;
        }

        foreach ($records as $record) {
            $id       = $record->id;
            $version  = $record->version;
            $filename = $version.'.yml';

            $output->writeln("<info>- Downgrading ({$version}.yml)</info>");

            $value = $yaml->parse(file_get_contents($dir.'/'.$filename));
            $down  = explode(';', trim($value['down']));
            $down  = array_filter($down);

            foreach ($down as $query) {
                $app['db']::statement(trim($query));
            }

            $app['db']->table('versions')->delete($id);
        }
    }
}
