#!/usr/bin/env php
<?php
declare(strict_types=1);

namespace App;

use App\Services\ConfigService;
use App\Services\FileService;
use App\Contracts\TransformInterface;

class Module
{
    public static function exec(string $configPath)
    {
        $fileService = new FileService();
        
        $config = new ConfigService(
            $fileService->load($configPath)
        );

        // Get each file configuration
        foreach ($config->getConfig() as $file => $transforms){
            echo "Processing {$file}\n";

            // Try load localfile
            try {
                $local = $fileService->load(
                    BASE_PATH . $file
                );
            } catch (\Exception $e) {
                echo "FAILED to load local file, creating new\n";
                $local = [];
            }
            
            // Try load remotefile
            try {
                $remote = $fileService->load(
                    REPO_PATH . $file
                );
            } catch (\Exception $e) {
                echo "FAILED to load remote file, ignoring\n";
                continue;
            }

            // Transform each key using the specified transform class
            foreach ($transforms as $key => $value) {
                if(!isset($remote[$key])) {
                    echo " - Key {$key} does not exist in remote file, ignoring\n";
                    continue;
                }
                $transform = "App\\Transforms\\{$value}";

                if (!class_exists($transform)) {
                    echo " - Transform class {$transform} does not exist, ignoring\n";
                    continue;
                }

                echo " - Transforming {$key} using {$value}\n";
            
                /**
                * @var TransformInterface $comparer 
                */
                $comparer = new $transform();

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
