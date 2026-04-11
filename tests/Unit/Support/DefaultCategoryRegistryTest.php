<?php
declare(strict_types=1);

namespace Sprout\Propagator\Tests\Unit\Support;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sprout\Propagator\Contracts\ConfigCategory;
use Sprout\Propagator\Support\DefaultCategoryRegistry;

#[CoversClass(DefaultCategoryRegistry::class)]
final class DefaultCategoryRegistryTest extends TestCase
{
    #[Test]
    public function it_registers_and_retrieves_a_category(): void
    {
        $registry = new DefaultCategoryRegistry();
        $category = $this->createMock(ConfigCategory::class);
        $category->method('getName')->willReturn('resolvers');

        $registry->register($category);

        $this->assertSame($category, $registry->get('resolvers'));
    }

    #[Test]
    public function it_checks_if_a_category_exists(): void
    {
        $registry = new DefaultCategoryRegistry();
        $category = $this->createMock(ConfigCategory::class);
        $category->method('getName')->willReturn('resolvers');

        $this->assertFalse($registry->has('resolvers'));

        $registry->register($category);

        $this->assertTrue($registry->has('resolvers'));
    }

    #[Test]
    public function it_returns_all_categories(): void
    {
        $registry = new DefaultCategoryRegistry();

        $cat1 = $this->createMock(ConfigCategory::class);
        $cat1->method('getName')->willReturn('resolvers');

        $cat2 = $this->createMock(ConfigCategory::class);
        $cat2->method('getName')->willReturn('providers');

        $registry->register($cat1);
        $registry->register($cat2);

        $all = $registry->all();

        $this->assertCount(2, $all);
        $this->assertArrayHasKey('resolvers', $all);
        $this->assertArrayHasKey('providers', $all);
    }

    #[Test]
    public function it_throws_for_unknown_category(): void
    {
        $registry = new DefaultCategoryRegistry();

        $this->expectException(InvalidArgumentException::class);

        $registry->get('nonexistent');
    }
}
