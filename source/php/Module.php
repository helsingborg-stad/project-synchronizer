#!/usr/bin/env php
<?php
declare(strict_types=1);

namespace App;

use App\Services\ConfigService;
use App\Services\FileService;
use App\Contracts\TransformInterface;

class Module
{
    public static function exec()
    {
        $fileService = new FileService();
        $config = new ConfigService($fileService);

        // Get each file configuration
        foreach ($config->getConfig() as $file => $transforms){
            echo "Checking {$file}\n";

            // Try load localfile
            try {
                $local = $fileService->fetchLocalFile(
                    BASE_PATH . $file
                );
            } catch (\Exception $e) {
                echo "Could not fetch local file {$file}, creating new\n";
                $local = [];
            }
            
            // Try load remotefile
            try {
                $remote = $fileService->fetchRemoteFile(
                    $config->getRemoteRepoPath(), 
                    $file
                );
            } catch (\Exception $e) {
                echo "Could not fetch remote file {$file} from repo ".$config->getRemoteRepoPath()."\n";
                continue;
            }

            foreach ($transforms as $key => $value) {
                if(!isset($remote[$key])) {
                    continue;
                }
                echo " - Transforming {$key} using {$value}\n";
                $transform = "App\\Transforms\\{$value}";
            
                /**
 * @var TransformInterface $comparer 
*/
                $comparer = new $transform();

                $local[$key] = $comparer->transform(
                    $remote[$key],
                    $local[$key] ?? []
                );
                
            }
            $fileService->saveLocalFile(
                BASE_PATH . $file,
                $local
            );
        }
    }
};
