<?php

declare(strict_types=1);

namespace App\Contracts;

interface TransformInterface
{
    public function transform(mixed $source, mixed $target, ConfigServiceInterface $config): mixed;
}
