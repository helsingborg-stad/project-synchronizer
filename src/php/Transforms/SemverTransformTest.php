<?php
declare(strict_types=1);

namespace App\Services\Tests;

use App\Transforms\SemverTransform;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class SemverTransformTest extends TestCase {
    protected function setUp(): void {
    }

    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated() {
        $transform = new SemverTransform();
        $this->assertInstanceOf(SemverTransform::class, $transform);
    }

    #[TestDox('Add missing dependencies')]
    public function testAddMissingDependencies() {
        $transform = new SemverTransform();

        $result = $transform->transform([
            // Reference
            "libraryA" => "1.0.0",
        ], [
            // Target
            "libraryB" => "2.0.0",
        ]);
        $this->assertEquals([
            // Target after transform
            "libraryB" => "2.0.0",
            "libraryA" => "1.0.0",
        ], $result);
    }

    #[TestDox('Update existing dependencies')]
    public function testUpdateExistingDependencies() {
        $transform = new SemverTransform();

        $result = $transform->transform([
            // Reference
            "libraryA" => "2.0.0",
        ], [
            // Target
            "libraryA" => "1.0.0",
            "libraryB" => "2.0.0",
        ]);
        $this->assertEquals([
            // Target after transform
            "libraryA" => "2.0.0",
            "libraryB" => "2.0.0",
        ], $result);
    }

    #[TestDox('Ignore existing dependencies')]
    public function testIgnoreExistingDependencies() {
        $transform = new SemverTransform();

        $result = $transform->transform([
            // Reference
            "libraryA" => "2.0.0",
        ], [
            // Target
            "libraryA" => "2.0.0",
            "libraryB" => "2.0.0",
        ]);
        $this->assertEquals([
            // Target after transform
            "libraryA" => "2.0.0",
            "libraryB" => "2.0.0",
        ], $result);
    }
}