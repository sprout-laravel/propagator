<?php
declare(strict_types=1);

namespace Sprout\Propagator\Tests\Unit\Fields;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sprout\Propagator\Fields\SelectField;

#[CoversClass(SelectField::class)]
final class SelectFieldTest extends TestCase
{
    #[Test]
    public function it_creates_a_select_field(): void
    {
        $field = SelectField::make('driver');

        $this->assertSame('driver', $field->getName());
        $this->assertSame('select', $field->getType());
    }

    #[Test]
    public function it_stores_options(): void
    {
        $field = SelectField::make('driver')
            ->options(['subdomain' => 'Subdomain', 'header' => 'Header']);

        $this->assertSame(['subdomain' => 'Subdomain', 'header' => 'Header'], $field->getOptions());
    }

    #[Test]
    public function it_defaults_to_empty_options(): void
    {
        $field = SelectField::make('driver');

        $this->assertSame([], $field->getOptions());
    }
}
