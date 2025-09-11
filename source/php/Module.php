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

        $log->write(
            <<<TEXT
            =============================
            Project Synchronizer
            =============================
            
            Configuration: 
            {$cmd->config}
            
            Source folder/URL: 
            {$cmd->source}

            TEXT
        );

        try {
            // Try load config
            $conf = new ConfigService($fs->loadJSON($cmd->config));
        } catch (\Exception) {
            // Fatal error, cannot continue
            $log->write("Failed to read configuration file.");
            exit(1);
        }

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
        foreach ($conf->getFiles() as $file => $properties){
            // Track progress
            $log->write("Processing {$file}");

            // Define absolute paths for source and target files
            $tFile = $cmd->target . $file;
            $sFile = $cmd->source . $file;

            // An empty property list means an intent to copy the whole file
            // The file is only copied if it does not exist locally already. 
            // Since no transform will take place, this will allow any textbased 
            // filetype to be copied from source to target even if not JSON
            if(empty($properties)) {
                try {
                    $log->write(" - No properties configured, attempting full file copy.");
                    // Perform copy
                    $fs->copy(
                        $sFile,
                        $tFile,
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
                // Try load source file
                $remote = $fs->loadJSON($sFile);
            } catch (\Exception $e) {
                // Write error to log, but continue processing other files
                $log->write(
                    " - {$e->getMessage()}. " .
                    "If the file is missing in remote, and this is intentional, please remove the file from the configuration."
                );
                continue;
            }

            try {
                // Try load target file, if it exists
                $local = $fs->loadJSON($tFile);
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
                        " - Key {$key} does not exist in source file. " . 
                        "If this is intentional, please remove the property from the configuration."
                    );
                    continue;
                }
                // Track progress
                $log->write(" - Transforming {$key}");

                // Apply transformation
                $local[$key] = $tf->transform($remote[$key], $local[$key] ?? []);            
            }
            // Write changes to target file
            $fs->saveJSON($tFile, $local);
        }
    }
};
