<?php
declare(strict_types=1);

namespace Sprout\Propagator\Tests\Unit\Support;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sprout\Propagator\Contracts\DriverWizard;
use Sprout\Propagator\Support\DefaultDriverRegistry;

#[CoversClass(DefaultDriverRegistry::class)]
final class DefaultDriverRegistryTest extends TestCase
{
    #[Test]
    public function it_adds_and_retrieves_a_driver(): void
    {
        $registry = new DefaultDriverRegistry();
        $driver = $this->createMock(DriverWizard::class);
        $driver->method('getName')->willReturn('subdomain');

        $registry->add($driver);

        $this->assertSame($driver, $registry->get('subdomain'));
    }

    #[Test]
    public function it_checks_if_a_driver_exists(): void
    {
        $registry = new DefaultDriverRegistry();
        $driver = $this->createMock(DriverWizard::class);
        $driver->method('getName')->willReturn('subdomain');

        $this->assertFalse($registry->has('subdomain'));

        $registry->add($driver);

        $this->assertTrue($registry->has('subdomain'));
    }

    #[Test]
    public function it_returns_all_drivers(): void
    {
        $registry = new DefaultDriverRegistry();

        $d1 = $this->createMock(DriverWizard::class);
        $d1->method('getName')->willReturn('subdomain');

        $d2 = $this->createMock(DriverWizard::class);
        $d2->method('getName')->willReturn('header');

        $registry->add($d1);
        $registry->add($d2);

        $all = $registry->all();

        $this->assertCount(2, $all);
        $this->assertArrayHasKey('subdomain', $all);
        $this->assertArrayHasKey('header', $all);
    }

    #[Test]
    public function it_throws_for_unknown_driver(): void
    {
        $registry = new DefaultDriverRegistry();

        $this->expectException(InvalidArgumentException::class);

        $registry->get('nonexistent');
    }
}
