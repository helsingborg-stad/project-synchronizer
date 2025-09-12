#!/usr/bin/env php
<?php
declare(strict_types=1);

namespace App;

use App\Services\ConfigService;
use App\Services\FileService;
use App\Contracts\TransformInterface;

class Module {
    public static function exec() {
        $fileService = new FileService();
        $config = new ConfigService($fileService);

        # Get each file configuration
        foreach ($config->getConfig() as $file => $transforms){
            echo "Checking {$file}\n";

            # Try load localfile
            try {
                $local = $fileService->fetchLocalFile(
                    BASE_PATH . $file
                );
            } catch (\Exception $e) {
                echo "Could not fetch local file {$file}, creating new\n";
                $local = [];
            }
            
            # Try load remotefile
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
            
                /** @var TransformInterface $comparer */
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

/*

function usage() {
    echo "Usage: dep-checker.php --target=PATH --master=PATH\n";
    echo "  --target PATH   Path to the local package.json file (default: package.json)\n";
    echo "  --master PATH   Path to the master package.json file (default: master-package.json)\n";
    echo "  --help          Show this help message\n";
    exit(0);
}


function semver_compare_min_version(string $range): ?string {
    if (preg_match('/(\d+\.\d+\.\d+)/', $range, $matches)) {
        return $matches[1];
    }
    return null;
}

function semver_gt(string $v1, string $v2): bool {
    $parts1 = explode('.', $v1);
    $parts2 = explode('.', $v2);
    for ($i = 0; $i < 3; $i++) {
        $p1 = intval($parts1[$i] ?? 0);
        $p2 = intval($parts2[$i] ?? 0);
        if ($p1 > $p2) return true;
        if ($p1 < $p2) return false;
    }
    return false;
}

function checkDeps(array $target, array $source): array {
    $missing = [];
    $outdated = [];

    foreach ($source as $pkg => $srcVersion) {
        if (!isset($target[$pkg])) {
            $missing[$pkg] = $srcVersion;
        } else {
            $tgtVersion = $target[$pkg];

            $srcMin = semver_compare_min_version($srcVersion);
            $tgtMin = semver_compare_min_version($tgtVersion);

            if ($srcMin !== null && $tgtMin !== null && semver_gt($srcMin, $tgtMin)) {
                $outdated[$pkg] = ['current' => $tgtVersion, 'required' => $srcVersion];
            }
        }
    }

    return ['missing' => $missing, 'outdated' => $outdated];
}

// Parse CLI arguments
$opt
*/