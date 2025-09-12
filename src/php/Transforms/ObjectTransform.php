<?php
declare(strict_types=1);

namespace App\Transforms;

use App\Contracts\TransformInterface;

class ObjectTransform implements TransformInterface {
    public function transform($reference, $target): mixed {
        if (is_array($reference) || is_object($reference)) {        
            foreach ($reference as $name => $value) {
                $target[$name] = $value;
            }
            return $target;
        }
        return $reference;
    }
}