<?php

namespace App\Tests;

use App\Utils\Semver;
use PHPUnit\Framework\TestCase;

class SemverTest extends TestCase
{
    public function testGetMinVersionWithCaret()
    {
        $this->assertEquals('1.2.3', Semver::getMinVersion('^1.2.3'));
    }

    public function testGetMinVersionWithGreaterThan()
    {
        $this->assertEquals('2.0.0', Semver::getMinVersion('>=2.0.0'));
    }

    public function testGetMinVersionWithTildeShort()
    {
        $this->assertEquals('3.1.0', Semver::getMinVersion('~3.1'));
    }

    public function testGetMinVersionInvalid()
    {
        $this->assertNull(Semver::getMinVersion('abc'));
    }

    public function testIsGreaterThan()
    {
        $this->assertTrue(Semver::isGreaterThan('1.2.4', '1.2.3'));
        $this->assertFalse(Semver::isGreaterThan('1.2.3', '1.2.3'));
        $this->assertFalse(Semver::isGreaterThan('1.2.2', '1.2.3'));
    }

    public function testIsLessThan()
    {
        $this->assertTrue(Semver::isLessThan('1.2.2', '1.2.3'));
        $this->assertFalse(Semver::isLessThan('1.2.3', '1.2.2'));
    }

    public function testIsEqual()
    {
        $this->assertTrue(Semver::isEqual('1.2.3', '1.2.3'));
        $this->assertFalse(Semver::isEqual('1.2.3', '1.2.4'));
    }
}
