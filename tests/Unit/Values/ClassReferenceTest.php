<?php
declare(strict_types=1);

namespace Sprout\Propagator\Tests\Unit\Values;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sprout\Propagator\Values\ClassReference;

#[CoversClass(ClassReference::class)]
final class ClassReferenceTest extends TestCase
{
    #[Test]
    public function it_stores_fully_qualified_class_name(): void
    {
        $ref = new ClassReference(\stdClass::class);

        $this->assertSame(\stdClass::class, $ref->fqcn);
    }
}
