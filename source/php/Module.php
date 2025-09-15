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
        // Create services
        $log = new ConsoleLoggerService();
        $fs = new FileService();
        $tf = new Transform($log);
        $conf = new ConfigService($fs->loadJSON($cmd->config));

      /** 
       * Process files
       * In the below example, the files processed are 
       * /package.json and /composer.json
       * 
       * Example config:
       *   {
       *       "/package.json": [...],
       *       "/composer.json": [...],
       *       "....": [...]
       *   }
       */
        foreach ($conf->getConfig() as $file => $properties){
            // Track progress
            $log->write("Processing {$file}");

            // Define fully qualified paths for local and remote files
            $lFile = $cmd->base . $file;
            $rFile = $cmd->source . $file;

            // An empty property list means an intent to copy the whole file
            // The file is only copied if it does not exist locally already. 
            // Since no transform will take place, this will allow any textbased 
            // filetype to be copied from remote to local
            if(empty($properties)) {
                try {
                    $log->write(" - No properties configured, copymode enabled.");
                    // Perform copy
                    $fs->copy(
                        $rFile,
                        $lFile,
                        !$cmd->overwrite
                    );
                } catch (\Exception $e) {
                    // Write error to log, but continue processing other files
                    $log->write(" - {$e->getMessage()}");
                }
                continue;
            }

            /**
             * Only JSON based files can be transformed
             */
            try {
                // Try load remotefile
                $remote = $fs->loadJSON($rFile);
            } catch (\Exception $e) {
                // Write error to log, but continue processing other files
                $log->write(
                    " - {$e->getMessage()}. " .
                    "If the file is missing in remote, and this is intentional, please remove the file from the configuration."
                );
                continue;
            }

            try {
                // Try load localfile
                $local = $fs->loadJSON($lFile);
            } catch (\Exception $e) {
                // Write error to log, but continue processing
                $log->write(
                    " - {$e->getMessage()}. " . 
                    "The configured properties will be copied from remote into a new file"
                );
                $local = [];
            }
            
            /**
             * Example properties
             * {
             *      "/...": [
             *          "require", 
             *          "require-dev", 
             *          "scripts"
             *      ]
             * }
             */
            foreach ($properties as $key) {
                // Configured key does not exist in remote file
                if (!isset($remote[$key])) {
                    $log->write(
                        " - Key {$key} does not exist in remote file. " . 
                        "If this is intentional, please remove the property from the configuration."
                    );
                    continue;
                }
                // Track progress
                $log->write(" - Transforming {$key}");

                // Apply transformation
                $local[$key] = $tf->transform($remote[$key], $local[$key] ?? []);            
            }
            // Write changes to local file
            $fs->saveJSON($lFile, $local);
        }
    }
};
