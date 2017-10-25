<?php

use Silex\Application;
use Groovey\DB\Providers\DBServiceProvider;
use Groovey\Tester\Providers\TesterServiceProvider;
use Groovey\Version\Commands\About;
use Groovey\Version\Commands\Init;
use Groovey\Version\Commands\Reset;
use Groovey\Version\Commands\Listing;
use Groovey\Version\Commands\Status;
use Groovey\Version\Commands\Up;
use Groovey\Version\Commands\Down;
use Groovey\Version\Commands\Drop;

class VersionTest extends PHPUnit_Framework_TestCase
{
    public $app;

    public function setUp()
    {
        $app = new Application();
        $app['debug'] = true;

        $app->register(new TesterServiceProvider());

        $app->register(new DBServiceProvider(), [
            'db.connection' => [
                'host'      => 'localhost',
                'driver'    => 'mysql',
                'database'  => 'test_version',
                'username'  => 'root',
                'password'  => '',
                'charset'   => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'prefix'    => '',
                'logging'   => true,
            ],
        ]);

        $app['tester']->add([
                new About(),
                new Init($app),
                new Reset($app),
                new Status($app),
                new Up($app),
                new Listing($app),
                new Down($app),
                new Drop($app),
            ]);

        $this->app = $app;
    }

    public function testAbout()
    {
        $app = $this->app;
        $display = $app['tester']->command('version:about')->execute()->display();
        $this->assertRegExp('/Groovey/', $display);
    }

    public function testInit()
    {
        $app = $this->app;
        $display = $app['tester']->command('version:init')->execute()->display();
        $this->assertRegExp('/Sucessfully/', $display);
    }

    public function testReset()
    {
        $app = $this->app;
        $display = $app['tester']->command('version:reset')->input('Y\n')->execute()->display();
        $this->assertRegExp('/All version entries has been cleared/', $display);
    }

    public function testStatus()
    {
        $app = $this->app;
        $display = $app['tester']->command('version:status')->execute()->display();
        $this->assertRegExp('/Nothing to migrate/', $display);
    }

    public function testUp()
    {
        $app = $this->app;
        $display = $app['tester']->command('version:up')->input('Y\n')->execute()->display();
        $this->assertRegExp('/No new files to be migrated/', $display);
    }

    public function testListing()
    {
        $app = $this->app;
        $display = $app['tester']->command('version:list')->execute()->display();

        $this->assertRegExp('/Version | Changelog/', $display);
    }

    public function testDown()
    {
        $app = $this->app;
        $display = $app['tester']->command('version:down')->input('Y\n')->execute()->display();
        $this->assertRegExp('/Nothing to downgrade/', $display);
    }

    public function testDrop()
    {
        $app = $this->app;
        $display = $app['tester']->command('version:drop')->input('Y\n')->execute()->display();
        $this->assertRegExp('/Versions table has been deleted/', $display);
    }
}
