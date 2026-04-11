<?php
declare(strict_types=1);

namespace Sprout\Propagator\Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Sprout\Propagator\Contracts\CategoryRegistry;
use Sprout\Propagator\Contracts\WizardRenderer;
use Sprout\Propagator\PropagatorServiceProvider;
use Sprout\Propagator\Renderers\CliWizardRenderer;
use Sprout\Propagator\Support\ConfigWriter;
use Sprout\Propagator\Support\DefaultCategoryRegistry;
use Sprout\Propagator\Tests\Unit\UnitTestCase;

#[CoversClass(PropagatorServiceProvider::class)]
final class PropagatorServiceProviderTest extends UnitTestCase
{
    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            PropagatorServiceProvider::class,
        ];
    }

    #[Test]
    public function it_binds_category_registry_as_singleton(): void
    {
        $instance = $this->app->make(CategoryRegistry::class);

        $this->assertInstanceOf(DefaultCategoryRegistry::class, $instance);
        $this->assertSame($instance, $this->app->make(CategoryRegistry::class));
    }

    #[Test]
    public function it_binds_wizard_renderer(): void
    {
        $this->assertInstanceOf(
            CliWizardRenderer::class,
            $this->app->make(WizardRenderer::class),
        );
    }

    #[Test]
    public function it_binds_config_writer(): void
    {
        $this->assertInstanceOf(
            ConfigWriter::class,
            $this->app->make(ConfigWriter::class),
        );
    }

    #[Test]
    public function it_registers_built_in_categories(): void
    {
        $registry = $this->app->make(CategoryRegistry::class);

        $this->assertTrue($registry->has('resolvers'));
        $this->assertTrue($registry->has('providers'));
        $this->assertTrue($registry->has('tenancies'));
        $this->assertTrue($registry->has('overrides'));
    }

    #[Test]
    public function it_registers_the_sprout_config_command(): void
    {
        $commands = Artisan::all();

        $this->assertArrayHasKey('sprout:config', $commands);
    }

    #[Test]
    public function it_merges_propagator_config(): void
    {
        $mode = $this->app['config']->get('propagator.mode');

        $this->assertSame('managed', $mode);
    }
}
