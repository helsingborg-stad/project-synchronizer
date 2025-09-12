<?php
declare(strict_types=1);

namespace App\Services\Tests;

use App\Transforms\TextTransform;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class TextTransformTest extends TestCase {
    protected function setUp(): void {
    }

    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated() {
        $transform = new TextTransform();
        $this->assertInstanceOf(TextTransform::class, $transform);
    }

    #[TestDox('Add missing pairs')]
    public function testAddMissingPairs() {
        $transform = new TextTransform();

        $result = $transform->transform([
            // Reference
            "NameA" => "ValueA",
        ], [
            // Target
            "NameB" => "ValueB",
        ]);
        $this->assertEquals([
            // Target after transform
            "NameB" => "ValueB",
            "NameA" => "ValueA",
        ], $result);
    }

    #[TestDox('Update existing pairs')]
    public function testUpdateExistingPairs() {
        $transform = new TextTransform();

        $result = $transform->transform([
            // Reference
            "NameA" => "ValueA",
        ], [
            // Target
            "NameA" => "ValueA_Old",
        ]);
        $this->assertEquals([
            // Target after transform
            "NameA" => "ValueA",
        ], $result);
    }
}