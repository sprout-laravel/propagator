<?php
declare(strict_types=1);

namespace Sprout\Propagator\Tests\Unit\Categories;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sprout\Propagator\Categories\TenanciesCategory;

#[CoversClass(TenanciesCategory::class)]
final class TenanciesCategoryTest extends TestCase
{
    #[Test]
    public function it_has_the_correct_metadata(): void
    {
        $category = new TenanciesCategory();

        $this->assertSame('tenancies', $category->getName());
        $this->assertSame('Tenancies', $category->getLabel());
        $this->assertSame('multitenancy', $category->getConfigFile());
        $this->assertSame('tenancies', $category->getConfigKey());
    }

    #[Test]
    public function it_has_the_default_tenancy_driver(): void
    {
        $category = new TenanciesCategory();

        $this->assertTrue($category->drivers()->has('default'));
        $this->assertCount(1, $category->drivers()->all());
    }
}
