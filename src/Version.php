<?php

namespace Groovey\Version;

use Symfony\Component\Finder\Finder;

class Version
{
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public static function getTemplate()
    {
        $now  = new \DateTime();
        $date = $now->format('Y-m-d H:i:s');

        $yaml = <<<YML
date: '$date'
changelog: >

up: >


down: >

YML;

        return $yaml;
    }

    public static function getDirectory()
    {
        return getcwd().'/vendor/groovey/framework/database/versions';
    }

    public static function getAllFiles()
    {
        $finder = new Finder();

        return $finder->files()->in(self::getDirectory());
    }

    public static function getFileInfo($file)
    {
        $version   = substr($file, 0, -4);
        list($major, $minor) = explode('.', $version);

        return [
            'version' => $version,
            'major'   => $major,
            'minor'   => $minor,
        ];
    }

    public static function getUnMigratedFiles($app)
    {
        $migratedFiles = self::getMigratedFiles($app);
        $files         = [];

        foreach (self::getAllFiles() as $file) {
            $filename = $file->getRelativePathname();
            if (!in_array($filename, $migratedFiles)) {
                $files[] = $filename;
            }
        }

        return (array) $files;
    }

    public static function getMigratedFiles($app)
    {
        $files = [];
        $datas = $app['db']->table('versions')->orderBy('version')->get();
        foreach ($datas as $data) {
            $files[] = $data->version.'.yml';
        }

        return $files;
    }

    public static function getNextVersion($app)
    {
        $migratedFiles   = self::getMigratedFiles($app);
        $unMigratedFiles = self::getUnMigratedFiles($app);

        if (!$migratedFiles && !$unMigratedFiles) {
            return '0.1';
        }

        if (!$unMigratedFiles) {
            $last = end($migratedFiles);
        } else {
            $last = end($unMigratedFiles);
        }

        list($major, $minor) = explode('.', $last);

        return implode('.', [$major, $minor + 1]);
    }
}
