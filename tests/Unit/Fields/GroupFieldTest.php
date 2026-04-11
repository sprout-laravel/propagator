<?php
declare(strict_types=1);

namespace Sprout\Propagator\Tests\Unit\Fields;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sprout\Propagator\Fields\BooleanField;
use Sprout\Propagator\Fields\GroupField;
use Sprout\Propagator\Fields\TextField;

#[CoversClass(GroupField::class)]
final class GroupFieldTest extends TestCase
{
    #[Test]
    public function it_creates_a_group_field(): void
    {
        $field = GroupField::make('options');

        $this->assertSame('options', $field->getName());
        $this->assertSame('group', $field->getType());
    }

    #[Test]
    public function it_stores_nested_fields(): void
    {
        $children = [
            TextField::make('path')->label('Path')->default('/'),
            BooleanField::make('secure')->label('Secure')->default(true),
        ];

        $field = GroupField::make('options')
            ->label('Cookie Options')
            ->fields($children);

        $this->assertCount(2, $field->getFields());
        $this->assertSame('path', $field->getFields()[0]->getName());
        $this->assertSame('secure', $field->getFields()[1]->getName());
    }

    #[Test]
    public function it_defaults_to_empty_fields(): void
    {
        $field = GroupField::make('options');

        $this->assertSame([], $field->getFields());
    }
}
