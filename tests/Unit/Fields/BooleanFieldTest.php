<?php
declare(strict_types=1);

namespace Sprout\Propagator\Tests\Unit\Fields;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sprout\Propagator\Fields\BooleanField;

#[CoversClass(BooleanField::class)]
final class BooleanFieldTest extends TestCase
{
    #[Test]
    public function it_creates_a_boolean_field(): void
    {
        $field = BooleanField::make('secure');

        $this->assertSame('secure', $field->getName());
        $this->assertSame('boolean', $field->getType());
    }

    #[Test]
    public function it_supports_fluent_configuration(): void
    {
        $field = BooleanField::make('secure')
            ->label('Secure')
            ->default(true);

        $this->assertSame('Secure', $field->getLabel());
        $this->assertTrue($field->getDefault());
    }
}
