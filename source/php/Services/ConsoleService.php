<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\ConsoleServiceInterface;

class ConsoleService implements ConsoleServiceInterface
{
    public function write(string $text, ...$args): void
    {
        echo sprintf($text, ...$args);
    }

    public function writeLn(string $text, ...$args): void
    {
        $this->write($text . PHP_EOL, ...$args);
    }
}
