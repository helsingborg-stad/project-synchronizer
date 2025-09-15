#!/usr/bin/env php
<?php
declare(strict_types=1);

namespace App;

use App\Services\ConfigService;
use App\Services\ConsoleLoggerService;
use App\Services\FileService;
use App\Transforms\Transform;

class Module
{
    public static function exec(object $cmd)
    {
        $logService = new ConsoleLoggerService();
        $fileService = new FileService();
        
        $config = new ConfigService(
            $fileService->load($cmd->config),
            $logService
        );

        // Get each file configuration
        foreach ($config->getConfig() as $file => $transforms){
            $logService->write("Processing {$file}");

            // Try load localfile
            try {
                $local = $fileService->load(
                    BASE_PATH . $file
                );
            } catch (\Exception $e) {
                $logService->write("FAILED to load local file, creating new");
                $local = [];
            }
            
            // Try load remotefile
            try {
                $remote = $fileService->load(
                    REPO_PATH . $file
                );
            } catch (\Exception $e) {
                $logService->write("FAILED to load remote file, ignoring");
                continue;
            }

            // Transform each key using the specified transform class
            foreach ($transforms as $key) {
                if(!isset($remote[$key])) {
                    $logService->write(" - Key {$key} does not exist in remote file, ignoring");
                    continue;
                }

                $logService->write(" - Transforming {$key}");

                $comparer = new Transform();

                $local[$key] = $comparer->transform(
                    $remote[$key],
                    $local[$key] ?? []
                );
                
            }
            $fileService->save(
                BASE_PATH . $file,
                $local
            );
        }
    }
};
