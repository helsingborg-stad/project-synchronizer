<?php

declare(strict_types=1);

namespace App\Contracts;

interface ConsoleServiceInterface
{
    public function write(string $text, ...$args): void;

    public function writeLn(string $text, ...$args): void;
}
