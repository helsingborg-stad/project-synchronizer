<?php

declare(strict_types=1);

namespace App\Transforms;

use App\Contracts\ConfigServiceInterface;
use App\Contracts\TransformInterface;
use Composer\Semver\Comparator;
use Composer\Semver\Constraint\Bound;
use Composer\Semver\VersionParser;

class Transform implements TransformInterface
{
    private function getLowerBound(string $version): Bound
    {
        $parser = new VersionParser();
        return $parser->parseConstraints($version)->getLowerBound();
    }

    private function parse(string $source, string $target, bool $overwrite): mixed
    {
        if ($overwrite) {
            return $source;
        }
        return $this->doParse($source, $target);
    }

    private function doParse(string $source, string $target): mixed
    {
        if ($source !== $target) {
            try {
                $v1 = $this->getLowerBound($source);
                $v2 = $this->getLowerBound($target);

                if (Comparator::greaterThan($v1, $v2)) {
                    return $source;
                }
            } catch (\UnexpectedValueException) {
            }
        }
        return $target;
    }

    private function merge(array $source, array $target, bool $overwrite): array
    {
        if ($overwrite) {
            return $source;
        }
        return $this->doMerge($source, $target);
    }

    private function doMerge(array $source, array $target): array
    {
        if (array_intersect($source, $target) === $source) {
            return $target;
        }
        return array_values(array_unique(array_merge($source, $target)));
    }

    public function transform(mixed $source, mixed $target, ConfigServiceInterface $config): mixed
    {
        if (null === $target) {
            return $source;
        }
        if (is_string($source)) {
            return $this->parse($source, $target, $config->getForce());
        }
        if (is_array($source) && array_is_list($source)) {
            return $this->merge($source, $target, $config->getForce());
        }
        if (is_array($source) || is_object($source)) {
            foreach ($source as $name => $value) {
                $target[$name] = $this->transform($value, $target[$name] ?? null, $config);
            }
        }
        return $target;
    }
}
