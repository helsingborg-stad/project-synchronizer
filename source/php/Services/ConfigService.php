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
        $this->setForce(isset($cmd->force));
        $this->setSourcePath($cmd->source ?? '');
        $this->setTargetPath($cmd->target ?? '');
        $this->setConfigPath($cmd->config ?? '');
        $this->files = [];
    }

    public function getSourcePath(): string
    {
        return $this->source;
    }

    public function setSourcePath(string $path): void
    {
        $this->source = rtrim($path, '/');
    }

    public function getTargetPath(): string
    {
        return $this->target;
    }

    public function setTargetPath(string $path): void
    {
        $this->target = rtrim($path, '/');
    }

    public function getConfigPath(): string
    {
        return $this->config;
    }

    public function setConfigPath(string $path): void
    {
        $this->config = rtrim($path, '/');
    }

    public function getForce(): bool
    {
        return $this->force;
    }

    public function setForce(bool $force): void
    {
        $this->force = $force;
    }

    public function getFiles(): array
    {
        return $this->files;
    }

    public function setFiles(array $files): void
    {
        $this->files = $this->normalizeFiles($files);
    }

    public function loadConfig(FileServiceInterface $fs): void
    {
        $config = $fs->loadJSON($this->config);

        if (isset($config['source'])) {
            $this->setSourcePath($config['source']);
        }
        if (isset($config['target'])) {
            $this->setTargetPath($config['target']);
        }
        if (isset($config['force'])) {
            $this->force = (bool) $config['force'];
        }
        $this->setFiles($config['files'] ?? []);
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
