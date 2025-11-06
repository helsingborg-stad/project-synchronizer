<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\ConsoleServiceInterface;

class NullConsoleService implements ConsoleServiceInterface
{
    public function write(string $row): void
    {
    }
    public function writeLn(string $text, ...$args): void
    {
    }
}
