<?php
/**
 * VI-74 FileLoggerService
 *
 * @category   Service
 * @package    Virtua_LoggingService
 * @copyright  Copyright (c) Virtua
 * @author     Mateusz Soboń <m.sobon@wearevirtua.com>
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