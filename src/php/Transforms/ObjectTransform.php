<?php
declare(strict_types=1);

namespace App\Transforms;

use App\Contracts\TransformInterface;

class ObjectTransform implements TransformInterface {
    public function transform($reference, $target): array {
        foreach ($reference as $name => $value) {
            $target[$name] = $value;
        }
        return $target;
    }
}