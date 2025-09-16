<?php
declare(strict_types=1);

namespace App\Transforms;

use App\Contracts\TransformInterface;
use App\Contracts\LoggerServiceInterface;
use Composer\Semver\VersionParser;

class Transform implements TransformInterface
{
    public function __construct(private LoggerServiceInterface $log)
    {
    }

    private function getLowerBounds($v1, $v2): array
    {
        $parser = new VersionParser();        
        return [
            $parser->parseConstraints($v1)
                ->getLowerBound(), 
            $parser->parseConstraints($v2)
                ->getLowerBound()
        ];
    }

    private function parse(string $reference, string $target): mixed
    {
        try {
            [$v1, $v2] = $this->getLowerBounds($reference, $target);

            if ($v1 > $v2) {
                return $reference;
            }
        } catch (\UnexpectedValueException) {
            // Not a semver string, ignore
        }
        return $target;
    }

    private function merge(array $a, ?array $b): array
    {
        return array_values(
            array_unique(
                array_merge($a, $b ?? [])
            )
        );
    }

    public function transform(mixed $reference, mixed $target): mixed
    {
        // Target does not exist
        if (!isset($target)) {
            return $reference;
        }
        // Reference is a string (potentially semver)
        if (is_string($reference)) {
            return $this->parse($reference, $target);
        }
        // Reference is a list/array
        if (is_array($reference) && array_is_list($reference)) {
            return $this->merge($reference, $target);
        }
        // Reference is an associative array/object
        if(is_array($reference) || is_object($reference)) {
            foreach ($reference as $name => $value) {
                $target[$name] = $this->transform(
                    $value, $target[$name]
                );
            }
        }
        return $target;
    }
}