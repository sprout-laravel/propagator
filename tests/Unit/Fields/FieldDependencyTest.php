<?php
declare(strict_types=1);

namespace Sprout\Propagator\Tests\Unit\Fields;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sprout\Propagator\Fields\FieldDependency;

#[CoversClass(FieldDependency::class)]
final class FieldDependencyTest extends TestCase
{
    #[Test]
    public function it_creates_dependency_with_when(): void
    {
        $dep = FieldDependency::when('driver', 'subdomain');

        $this->assertSame('driver', $dep->getFieldName());
        $this->assertSame('subdomain', $dep->getExpectedValue());
    }

    #[Test]
    public function it_is_met_when_value_matches(): void
    {
        $dep = FieldDependency::when('driver', 'subdomain');

        $this->assertTrue($dep->isMet(['driver' => 'subdomain']));
    }

    #[Test]
    public function it_is_not_met_when_value_differs(): void
    {
        $dep = FieldDependency::when('driver', 'subdomain');

        $this->assertFalse($dep->isMet(['driver' => 'header']));
    }

    #[Test]
    public function it_is_not_met_when_field_is_absent(): void
    {
        $dep = FieldDependency::when('driver', 'subdomain');

        $this->assertFalse($dep->isMet([]));
    }
}
