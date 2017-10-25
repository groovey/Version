<?php

namespace Groovey\Version\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

class Listing extends Command
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
            ->setName('version:list')
            ->setDescription('Listing off all the versions script')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = $this->app;
        $versions = $app['db']->table('versions')->orderBy('version')->get();

        $datas = [];

        foreach ($versions as $version) {
            $datas[] = [
                'version'    => $version->version,
                'changelog'  => trim(wordwrap($version->changelog, 50)),
                'created at' => substr($version->created_at, 0, 10),
                'updated at' => substr($version->updated_at, 0, 10),
            ];
        }

        $table = new Table($output);
        $table->setColumnWidths(array(3, 50));
        $table
            ->setHeaders(['Version', 'Changelog', 'Created At', 'Updated At'])
            ->setRows($datas);

        $table->render();
    }
}
