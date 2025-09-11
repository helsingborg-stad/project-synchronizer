<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\ConfigServiceInterface;
use App\Contracts\FileServiceInterface;

class ConfigService implements ConfigServiceInterface
{
    private bool $force;
    private string $source;
    private string $target;
    private string $config;
    private array $files;

    public function __construct(object $cmd)
    {
        $this->force = isset($cmd->force);
        $this->source = rtrim($cmd->source ?? '', '/');
        $this->target = rtrim($cmd->target ?? '', '/');
        $this->config = $cmd->config ?? '';
        $this->files = [];
    }

    public function getSourcePath(): string
    {
        return $this->source;
    }

    public function getTargetPath(): string
    {
        return $this->target;
    }

    public function getConfigPath(): string
    {
        return $this->config;
    }

    public function getForce(): bool
    {
        return $this->force;
    }

    public function getFiles(): array
    {
        return $this->files;
    }

    public function loadFiles(FileServiceInterface $fs): void
    {
        $files = $fs->loadJSON($this->config);
        $this->files = $this->normalizeFiles($files);
    }

    private function normalizeFiles(array $content): array
    {
        foreach ($content as $key => $value) {
            if (!is_string($key) || !is_array($value) || !array_is_list($value)) {
                unset($content[$key]);
            } else {
                $nKey = '/' . ltrim($key, '/');
                if ($nKey !== $key) {
                    $content[$nKey] = $value;
                    unset($content[$key]);
                }
            }
        }
        return $content;
    }
}
