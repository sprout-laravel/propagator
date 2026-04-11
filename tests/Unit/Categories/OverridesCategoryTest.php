<?php
declare(strict_types=1);

namespace Sprout\Propagator\Tests\Unit\Categories;

use Illuminate\Config\Repository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sprout\Propagator\Categories\BaseConfigCategory;
use Sprout\Propagator\Categories\OverridesCategory;
use Sprout\Propagator\Values\ClassReference;

#[CoversClass(OverridesCategory::class)]
#[CoversClass(BaseConfigCategory::class)]
final class OverridesCategoryTest extends TestCase
{
    #[Test]
    public function it_has_the_correct_metadata(): void
    {
        $category = new OverridesCategory();

        $this->assertSame('overrides', $category->getName());
        $this->assertSame('Service Overrides', $category->getLabel());
        $this->assertSame('overrides', $category->getConfigFile());
        $this->assertSame('', $category->getConfigKey());
    }

    #[Test]
    public function it_reads_entries_from_root_level_config(): void
    {
        $category = new OverridesCategory();
        $config = new Repository([
            'overrides' => [
                'cache'   => ['driver' => 'App\\Override\\Cache'],
                'session' => ['driver' => 'App\\Override\\Session'],
            ],
        ]);

        $entries = $category->getEntries($config);

        $this->assertCount(2, $entries);
        $this->assertArrayHasKey('cache', $entries);
        $this->assertArrayHasKey('session', $entries);
    }

    #[Test]
    public function it_reads_a_single_entry_from_root_level_config(): void
    {
        $category = new OverridesCategory();
        $config = new Repository([
            'overrides' => [
                'cache' => ['driver' => 'App\\Override\\Cache'],
            ],
        ]);

        $entry = $category->getEntry('cache', $config);

        $this->assertNotNull($entry);
        $this->assertSame('App\\Override\\Cache', $entry['driver']);
    }

    #[Test]
    public function it_builds_entry_with_class_reference_driver(): void
    {
        $category = new OverridesCategory();
        $entry = $category->buildEntry('cache', 'App\\Overrides\\CacheOverride', []);

        $this->assertInstanceOf(ClassReference::class, $entry['driver']);
        $this->assertSame('App\\Overrides\\CacheOverride', $entry['driver']->fqcn);
    }
}
