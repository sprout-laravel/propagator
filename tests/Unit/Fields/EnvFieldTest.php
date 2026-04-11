<?php
declare(strict_types=1);

namespace Sprout\Propagator\Tests\Unit\Fields;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sprout\Propagator\Fields\EnvField;
use Sprout\Propagator\Fields\TextField;

#[CoversClass(EnvField::class)]
final class EnvFieldTest extends TestCase
{
    #[Test]
    public function it_creates_an_env_field(): void
    {
        $field = EnvField::make('domain');

        $this->assertSame('domain', $field->getName());
        $this->assertSame('env', $field->getType());
    }

    #[Test]
    public function it_stores_env_key(): void
    {
        $field = EnvField::make('domain')
            ->envKey('TENANTED_DOMAIN');

        $this->assertSame('TENANTED_DOMAIN', $field->getEnvKey());
    }

    #[Test]
    public function it_supports_field_fallback(): void
    {
        $fallback = TextField::make('default')->default('localhost');
        $field = EnvField::make('domain')
            ->envKey('TENANTED_DOMAIN')
            ->fallback($fallback);

        $this->assertSame($fallback, $field->getFallback());
    }

    #[Test]
    public function it_supports_nested_env_fallback(): void
    {
        $inner = EnvField::make('fallback')
            ->envKey('APP_DOMAIN')
            ->fallback(TextField::make('default')->default('localhost'));

        $outer = EnvField::make('domain')
            ->envKey('TENANTED_DOMAIN')
            ->fallback($inner);

        $this->assertInstanceOf(EnvField::class, $outer->getFallback());
        $this->assertSame('APP_DOMAIN', $outer->getFallback()->getEnvKey());
    }

    #[Test]
    public function it_has_no_fallback_by_default(): void
    {
        $field = EnvField::make('domain')->envKey('TENANTED_DOMAIN');

        $this->assertNull($field->getFallback());
    }
}
