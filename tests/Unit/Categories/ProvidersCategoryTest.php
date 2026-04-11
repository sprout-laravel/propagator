<?php
declare(strict_types=1);

namespace Sprout\Propagator\Tests\Unit\Categories;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sprout\Propagator\Categories\ProvidersCategory;
use Sprout\Propagator\Values\ClassReference;

#[CoversClass(ProvidersCategory::class)]
final class ProvidersCategoryTest extends TestCase
{
    #[Test]
    public function it_has_the_correct_metadata(): void
    {
        $category = new ProvidersCategory();
        $this->assertSame('providers', $category->getName());
        $this->assertSame('Tenant Providers', $category->getLabel());
        $this->assertSame('multitenancy', $category->getConfigFile());
        $this->assertSame('providers', $category->getConfigKey());
    }

    #[Test]
    public function it_has_eloquent_and_database_drivers(): void
    {
        $category = new ProvidersCategory();
        $this->assertTrue($category->drivers()->has('eloquent'));
        $this->assertTrue($category->drivers()->has('database'));
    }

    #[Test]
    public function it_builds_entry_with_driver_key(): void
    {
        $category = new ProvidersCategory();
        $entry = $category->buildEntry('tenants', 'eloquent', [
            'model' => new ClassReference('App\\Models\\Tenant'),
        ]);
        $this->assertSame('eloquent', $entry['driver']);
        $this->assertInstanceOf(ClassReference::class, $entry['model']);
    }
}
