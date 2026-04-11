<?php
declare(strict_types=1);

namespace Sprout\Propagator\Tests\Unit\Fields;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sprout\Propagator\Fields\BaseField;
use Sprout\Propagator\Fields\FieldDependency;
use Sprout\Propagator\Fields\TextField;

#[CoversClass(TextField::class)]
#[CoversClass(BaseField::class)]
final class TextFieldTest extends TestCase
{
    #[Test]
    public function it_creates_a_text_field_with_name(): void
    {
        $field = TextField::make('pattern');

        $this->assertSame('pattern', $field->getName());
        $this->assertSame('text', $field->getType());
    }

    #[Test]
    public function it_supports_fluent_configuration(): void
    {
        $field = TextField::make('domain')
            ->label('Tenanted Domain')
            ->default('localhost')
            ->required();

        $this->assertSame('domain', $field->getName());
        $this->assertSame('Tenanted Domain', $field->getLabel());
        $this->assertSame('localhost', $field->getDefault());
        $this->assertTrue($field->isRequired());
    }

    #[Test]
    public function it_defaults_label_to_name(): void
    {
        $field = TextField::make('domain');

        $this->assertSame('domain', $field->getLabel());
    }

    #[Test]
    public function it_supports_validation_rules(): void
    {
        $field = TextField::make('domain')
            ->rules(['min:3', 'max:255']);

        $this->assertSame(['min:3', 'max:255'], $field->getRules());
    }

    #[Test]
    public function it_supports_dependency(): void
    {
        $dep = FieldDependency::when('driver', 'subdomain');
        $field = TextField::make('domain')->dependsOn($dep);

        $this->assertSame($dep, $field->getDependency());
    }

    #[Test]
    public function it_has_no_dependency_by_default(): void
    {
        $field = TextField::make('domain');

        $this->assertNull($field->getDependency());
    }
}
