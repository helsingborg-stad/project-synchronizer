<?php

namespace App\Utils;

class Semver
{
    /**
     * Extract the minimum version from a semver string.
     * 
     * Examples:
     *   "^1.2.3"  => "1.2.3"
     *   ">=2.0.0" => "2.0.0"
     *   "~3.1"    => "3.1.0"
     */
    public static function getMinVersion(string $range): ?string
    {
        // Match the first version-like pattern
        if (preg_match('/(\d+\.\d+\.\d+)/', $range, $matches)) {
            return $matches[1];
        }

        // Handle short versions like ~3.1 => 3.1.0
        if (preg_match('/(\d+\.\d+)/', $range, $matches)) {
            return $matches[1] . '.0';
        }

        return null;
    }

    /**
     * Compare two semver versions.
     * Returns true if $v1 > $v2
     */
    public static function isGreaterThan(string $v1, string $v2): bool
    {
        $a = explode('.', $v1);
        $b = explode('.', $v2);

        for ($i = 0; $i < 3; $i++) {
            $aPart = intval($a[$i] ?? 0);
            $bPart = intval($b[$i] ?? 0);

            if ($aPart > $bPart) return true;
            if ($aPart < $bPart) return false;
        }

        return false; // versions are equal
    }

    /**
     * Compare two semver versions.
     * Returns true if $v1 < $v2
     */
    public static function isLessThan(string $v1, string $v2): bool
    {
        return self::isGreaterThan($v2, $v1);
    }

    /**
     * Returns true if $v1 == $v2
     */
    public static function isEqual(string $v1, string $v2): bool
    {
        return version_compare($v1, $v2, '=');
    }
}
