<?php
declare(strict_types=1);

namespace Sprout\Propagator\Tests\Unit\Values;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sprout\Propagator\Values\EnvValue;

#[CoversClass(EnvValue::class)]
final class EnvValueTest extends TestCase
{
    #[Test]
    public function it_stores_key_and_fallback(): void
    {
        $value = new EnvValue('APP_KEY', 'default');

        $this->assertSame('APP_KEY', $value->key);
        $this->assertSame('default', $value->fallback);
    }

    #[Test]
    public function it_allows_null_fallback(): void
    {
        $value = new EnvValue('APP_KEY');

        $this->assertSame('APP_KEY', $value->key);
        $this->assertNull($value->fallback);
    }

    #[Test]
    public function it_supports_nested_env_fallback(): void
    {
        $inner = new EnvValue('FALLBACK_KEY', 'localhost');
        $outer = new EnvValue('APP_KEY', $inner);

        $this->assertSame('APP_KEY', $outer->key);
        $this->assertInstanceOf(EnvValue::class, $outer->fallback);
        $this->assertSame('FALLBACK_KEY', $outer->fallback->key);
        $this->assertSame('localhost', $outer->fallback->fallback);
    }
}
