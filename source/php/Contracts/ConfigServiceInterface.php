<?php

declare(strict_types=1);

namespace App\Contracts;

interface ConfigServiceInterface
{
    public function getFiles(): array;

    public function setFiles(array $files): void;

    public function loadConfig(FileServiceInterface $fs): void;

    public function getSourcePath(): string;

    public function setSourcePath(string $path): void;

    public function getTargetPath(): string;

    public function setTargetPath(string $path): void;

    public function getConfigPath(): string;

    public function setConfigPath(string $path): void;

    public function getForce(): bool;

    public function setForce(bool $force): void;
}
