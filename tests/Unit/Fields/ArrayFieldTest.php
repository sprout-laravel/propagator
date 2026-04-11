<?php
declare(strict_types=1);

namespace Sprout\Propagator\Tests\Unit\Fields;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sprout\Propagator\Fields\ArrayField;

#[CoversClass(ArrayField::class)]
final class ArrayFieldTest extends TestCase
{
    #[Test]
    public function it_creates_an_array_field(): void
    {
        $field = ArrayField::make('items');

        $this->assertSame('items', $field->getName());
        $this->assertSame('array', $field->getType());
    }

    #[Test]
    public function it_supports_fluent_configuration(): void
    {
        $field = ArrayField::make('items')
            ->label('Items')
            ->default(['a', 'b']);

        $this->assertSame('Items', $field->getLabel());
        $this->assertSame(['a', 'b'], $field->getDefault());
    }
}
