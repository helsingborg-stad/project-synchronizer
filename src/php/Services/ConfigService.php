<?php
declare(strict_types=1);

namespace App\Services;

use App\Contracts\ConfigServiceInterface;
use App\Contracts\FileServiceInterface;

class ConfigService implements ConfigServiceInterface {
    private array $config = [];
    # The base path of the remote repository containing the master files
    # This will not likely ever change, but if it does, you can update it here
    private string $repoPath = "https://raw.githubusercontent.com/helsingborg-stad/municipio-dependency-master/refs/heads/main";

    public function __construct(FileServiceInterface $fileService)
    {
        $this->config = $this->normalize(
            $fileService->fetchRemoteFile(
                $this->repoPath, 
                '/config.json'
            )
        );
    }

    private function normalize(array $content): array {
       foreach ($content as $key => $value) {
        if(!is_array($value)) {
            $content[$key] = [];
        }
       }
       return $content;
    }

    public function getConfig(): array {
        return $this->config ?? []; 
    }
 
    public function getRemoteRepoPath(): string {
        return $this->repoPath;
    }
}