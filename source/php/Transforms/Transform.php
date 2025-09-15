<?php
declare(strict_types=1);

namespace App\Transforms;

use App\Contracts\TransformInterface;
use App\Contracts\LoggerServiceInterface;
use Composer\Semver\VersionParser;

class Transform implements TransformInterface
{
    public function __construct(private LoggerServiceInterface $log) {}

    private function getLowerBounds($v1, $v2): null|array {
        try {
            $parser = new VersionParser();
        
            return [
                $parser->parseConstraints($v1)
                    ->getLowerBound(), 
                $parser->parseConstraints($v2)
                    ->getLowerBound()
            ];
        } catch (\UnexpectedValueException) {
            return null;
        }
    }

    private function update(mixed $reference, mixed $target): mixed {
        // Target doesnt exist, return reference no matter type
        if($target === null) {
            return $reference;
        }
        // If value is a string, compare semver
        if (is_string($reference)) {
            [$v1, $v2] = $this->getLowerBounds($reference, $target);

            if (is_null($v1) || $v2 < $v1) {
                return $reference;
            }
        }
        // Leave target value by default
        return $target;
    }

    public function transform($reference, $target): mixed
    {
        // Array or Object
        if (is_array($reference) || is_object($reference)) {        
            foreach ($reference as $name => $value) {
                // Set new target value (add or update existing)
                $target[$name] = $this->update($value, $target[$name] ?? null);
            }
            return $target;
        }
        // String or other scalar
        return $this->update($reference, $target);
    }
}