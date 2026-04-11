<?php
declare(strict_types=1);

namespace Sprout\Propagator\Tests\Unit\Fields;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sprout\Propagator\Fields\ClassField;

#[CoversClass(ClassField::class)]
final class ClassFieldTest extends TestCase
{
    #[Test]
    public function it_creates_a_class_field(): void
    {
        $field = ClassField::make('model');

        $this->assertSame('model', $field->getName());
        $this->assertSame('class', $field->getType());
    }

    #[Test]
    public function it_supports_fluent_configuration(): void
    {
        $field = ClassField::make('model')
            ->label('Tenant Model')
            ->required();

        $this->assertSame('Tenant Model', $field->getLabel());
        $this->assertTrue($field->isRequired());
    }
}
