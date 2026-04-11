<?php
declare(strict_types=1);

namespace Sprout\Propagator\Tests\Unit\Values;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sprout\Propagator\Values\MethodCall;

#[CoversClass(MethodCall::class)]
final class MethodCallTest extends TestCase
{
    #[Test]
    public function it_stores_class_method_and_arguments(): void
    {
        $call = new MethodCall(\stdClass::class, 'doSomething', ['arg1', 'arg2']);

        $this->assertSame(\stdClass::class, $call->fqcn);
        $this->assertSame('doSomething', $call->method);
        $this->assertSame(['arg1', 'arg2'], $call->arguments);
    }

    #[Test]
    public function it_defaults_to_empty_arguments(): void
    {
        $call = new MethodCall(\stdClass::class, 'doSomething');

        $this->assertSame([], $call->arguments);
    }
}
