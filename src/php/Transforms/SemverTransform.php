<?php
declare(strict_types=1);

namespace App\Transforms;

use App\Contracts\TransformInterface;
use App\Utils\Semver;

class SemverTransform implements TransformInterface {
    public function transform($reference, $target): array {
        foreach ($reference as $name => $value) {
            if(!isset($target[$name]) || Semver::isLessThan($target[$name], $value)) {
                $target[$name] = $value;
            }
        }
       return $target;
    }
}