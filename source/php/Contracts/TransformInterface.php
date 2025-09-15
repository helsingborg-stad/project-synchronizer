<?php
declare(strict_types=1);

namespace App\Contracts;

interface TransformInterface
{
    public function transform(mixed $reference, mixed $target): mixed;
}

