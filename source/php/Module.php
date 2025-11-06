#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App;

use App\Contracts\ConfigServiceInterface;

define(
    'ERR_CONFIG_MISSING',
    'Failed to read configuration file. Make sure that a ps-config.json file exists at the target path, or that a valid URL is provided.',
);
define(
    'ERR_SOURCE_MISSING',
    'If the file is missing in source, and this is intentional, please remove the reference from the configuration.',
);
define(
    'ERR_KEY_MISSING',
    ' - Key %s does not exist in source file. If this is intentional, please remove the reference from the configuration.',
);

class Module
{
    public static function main(array $services)
    {
        [
            'console' => $console,
            'file' => $file,
            'transform' => $transform,
            'config' => $config,
        ] = $services;

        try {
            $config->loadConfig($file);
        } catch (\Exception) {
            $console->writeLn(ERR_CONFIG_MISSING);
            exit(1);
        }
        $console->writeLn(self::print($config));

        foreach ($config->getFiles() as $item => $properties) {
            $console->writeLn("Processing {$item}");

            $targetFile = $config->getTargetPath() . $item;
            $sourceFile = $config->getSourcePath() . $item;

            if (empty($properties) || !$file->exists($targetFile)) {
                try {
                    $file->copy($sourceFile, $targetFile, $config->getForce());
                    $console->writeLn(' - File copy successful.');
                } catch (\Exception $e) {
                    $console->writeLn(" - {$e->getMessage()}");
                }
                continue;
            }

            try {
                $sourceContent = $file->loadJSON($sourceFile);
            } catch (\Exception $e) {
                $console->writeLn(" - {$e->getMessage()}." . ERR_SOURCE_MISSING);
                continue;
            }

            try {
                $targetContent = $file->loadJSON($targetFile);
            } catch (\Exception $e) {
                $console->writeLn(" - {$e->getMessage()}");
                continue;
            }

            foreach ($properties as $key) {
                if (!isset($sourceContent[$key])) {
                    $console->writeLn(ERR_KEY_MISSING, $key);
                    continue;
                }
                $console->writeLn(" - Transforming {$key}");

                $targetContent[$key] = $transform->transform(
                    $sourceContent[$key],
                    $targetContent[$key] ?? null,
                    $config,
                );
            }
            $file->saveJSON($targetFile, $targetContent);
        }
    }

    private static function print(ConfigServiceInterface $config): string
    {
        return <<<TEXT

        ============================
        <=> Project Synchronizer <=>
        ============================

        {$config->toString()}

        TEXT;
    }
};
