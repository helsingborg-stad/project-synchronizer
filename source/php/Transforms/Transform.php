<?php
declare(strict_types=1);

namespace App\Transforms;

use App\Contracts\TransformInterface;
use Composer\Semver\VersionParser;
use Composer\Semver\Constraint\Bound;

class Transform implements TransformInterface
{
    private function getLowerBound(string $version): Bound
    {
        return (new VersionParser())
            ->parseConstraints($version)
            ->getLowerBound();
    }

    private function parse(string $source, string $target): mixed
    {
        if ($source !== $target) {
            try {
                // Try parse values as semver (throws UnexpectedValueException if not)
                $v1 = $this->getLowerBound($source);
                $v2 = $this->getLowerBound($target);

                // If source version is greater than target
                if ($v1 > $v2) {
                    return $source;
                }
            } catch (\UnexpectedValueException) {
                // Not a semver string
            }
        }
        // keep existing target
        return $target;
    }

    private function merge(array $source, array $target): array
    {
        // Reference items already in target, nothing to do
        if(array_intersect($source, $target) === $source) {
            return $target;
        }
        // Merge and remove duplicates
        return array_values(
            array_unique(
                array_merge($source, $target)
            )
        );
    }

    public function transform(mixed $source, mixed $target): mixed
    {
        // Target does not exist
        if (null === $target) {
            return $source;
        }
        // Reference is a string (potentially semver)
        if (is_string($source)) {
            return $this->parse($source, $target);
        }
        // Reference is a list/array
        if (is_array($source) && array_is_list($source)) {
            return $this->merge($source, $target);
        }
        // Reference is an associative array/object
        if(is_array($source) || is_object($source)) {
            foreach ($source as $name => $value) {
                $target[$name] = $this->transform(
                    $value, $target[$name] ?? null
                );
            }
        }
        return $target;
    }
}