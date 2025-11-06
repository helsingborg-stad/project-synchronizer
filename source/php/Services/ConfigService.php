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
    private bool $help;
    private array $files;

    public function __construct(object $cmd)
    {
        $this->setForce(isset($cmd->force));
        $this->setSourcePath($cmd->source ?? '');
        $this->setTargetPath($cmd->target ?? '');
        $this->setConfigPath($cmd->config ?? '');
        $this->setHelp(isset($cmd->help));
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

    public function setConfig(array $config): void
    {
        if (isset($config['source']) && is_string($config['source'])) {
            $this->setSourcePath($config['source']);
        }
        if (isset($config['target']) && is_string($config['target'])) {
            $this->setTargetPath($config['target']);
        }
        if (isset($config['force']) && is_bool($config['force'])) {
            $this->setForce((bool) $config['force']);
        }
        $this->setFiles($config['files'] ?? []);
    }

    public function getConfig(): array
    {
        return [
            'source' => $this->getSourcePath(),
            'target' => $this->getTargetPath(),
            'force' => $this->getForce(),
            'files' => $this->getFiles(),
        ];
    }

    public function setHelp(bool $help): void
    {
        $this->help = $help;
    }

    public function getHelp(): bool
    {
        return $this->help;
    }

    public function loadConfig(FileServiceInterface $fs): void
    {
        $this->setConfig($fs->loadJSON($this->config));
    }

    public function saveConfig(FileServiceInterface $fs): void
    {
        $fs->saveJSON($this->config, $this->getConfig(), $this->getForce());
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

    public function toString(): string
    {
        $force = $this->getForce() ? 'Yes' : 'No';

        return <<<TEXT
        - Configuration: {$this->getConfigPath()}
        - Source folder: {$this->getSourcePath()}
        - Target folder: {$this->getTargetPath()}
        - Force overwrite: {$force}
        TEXT;
    }
}
