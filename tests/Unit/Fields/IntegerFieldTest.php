<?php
declare(strict_types=1);

namespace Sprout\Propagator\Tests\Unit\Fields;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sprout\Propagator\Fields\IntegerField;

#[CoversClass(IntegerField::class)]
final class IntegerFieldTest extends TestCase
{
    #[Test]
    public function it_creates_an_integer_field(): void
    {
        $field = IntegerField::make('segment');

        $this->assertSame('segment', $field->getName());
        $this->assertSame('integer', $field->getType());
    }

    #[Test]
    public function it_supports_fluent_configuration(): void
    {
        $field = IntegerField::make('segment')
            ->label('Path Segment')
            ->default(1);

        $this->assertSame('Path Segment', $field->getLabel());
        $this->assertSame(1, $field->getDefault());
    }
}
