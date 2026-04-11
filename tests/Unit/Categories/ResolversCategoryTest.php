<?php
declare(strict_types=1);

namespace Sprout\Propagator\Tests\Unit\Categories;

use Illuminate\Config\Repository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sprout\Propagator\Categories\BaseConfigCategory;
use Sprout\Propagator\Categories\ResolversCategory;
use Sprout\Propagator\Values\EnvValue;

#[CoversClass(ResolversCategory::class)]
#[CoversClass(BaseConfigCategory::class)]
final class ResolversCategoryTest extends TestCase
{
    private ResolversCategory $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->category = new ResolversCategory();
    }

    #[Test]
    public function it_has_the_correct_name_and_label(): void
    {
        $this->assertSame('resolvers', $this->category->getName());
        $this->assertSame('Identity Resolvers', $this->category->getLabel());
    }

    #[Test]
    public function it_targets_the_multitenancy_config_file(): void
    {
        $this->assertSame('multitenancy', $this->category->getConfigFile());
        $this->assertSame('resolvers', $this->category->getConfigKey());
    }

    #[Test]
    public function it_has_all_built_in_resolver_drivers(): void
    {
        $drivers = $this->category->drivers();
        $this->assertTrue($drivers->has('subdomain'));
        $this->assertTrue($drivers->has('path'));
        $this->assertTrue($drivers->has('header'));
        $this->assertTrue($drivers->has('cookie'));
        $this->assertTrue($drivers->has('session'));
    }

    #[Test]
    public function it_reads_entries_from_config(): void
    {
        $config = new Repository([
            'multitenancy' => [
                'resolvers' => [
                    'subdomain' => ['driver' => 'subdomain', 'domain' => 'example.com'],
                    'header' => ['driver' => 'header'],
                ],
            ],
        ]);
        $entries = $this->category->getEntries($config);
        $this->assertCount(2, $entries);
        $this->assertArrayHasKey('subdomain', $entries);
        $this->assertArrayHasKey('header', $entries);
    }

    #[Test]
    public function it_reads_a_single_entry_from_config(): void
    {
        $config = new Repository([
            'multitenancy' => [
                'resolvers' => [
                    'subdomain' => ['driver' => 'subdomain', 'domain' => 'example.com'],
                ],
            ],
        ]);
        $entry = $this->category->getEntry('subdomain', $config);
        $this->assertNotNull($entry);
        $this->assertSame('subdomain', $entry['driver']);
    }

    #[Test]
    public function it_returns_null_for_missing_entry(): void
    {
        $config = new Repository(['multitenancy' => ['resolvers' => []]]);
        $this->assertNull($this->category->getEntry('nonexistent', $config));
    }

    #[Test]
    public function it_builds_an_entry_with_driver_key(): void
    {
        $entry = $this->category->buildEntry('subdomain', 'subdomain', [
            'domain' => new EnvValue('TENANTED_DOMAIN'),
            'pattern' => '.*',
        ]);
        $this->assertSame('subdomain', $entry['driver']);
        $this->assertInstanceOf(EnvValue::class, $entry['domain']);
        $this->assertSame('.*', $entry['pattern']);
    }
}
