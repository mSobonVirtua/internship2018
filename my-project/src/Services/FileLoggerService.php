<?php
/**
 * Created by PhpStorm.
 * User: virtua
 * Date: 28.06.2018
 * Time: 13:40
 */

namespace App\Services;


use Symfony\Component\Filesystem\Filesystem;

class FileLoggerService
{
    public function  logIntoFile(string $path, array $logs, string $name) : void
    {
        $fileSystem = new Filesystem();
        if(!$fileSystem->exists($path))
        {
            $fileSystem->dumpFile($path, '');
        }
        foreach ($logs as $log)
        {
            $fileSystem->appendToFile($path,
                "[LOG] \xA date=".date("Y-m-d H:i:s")." \xA ".$name."=". $log['name'] . " \xA reason=". $log['reason'] . "\xA"
            );
        }
    }
}