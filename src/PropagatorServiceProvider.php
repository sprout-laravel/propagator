<?php
declare(strict_types=1);

namespace Sprout\Propagator;

use Illuminate\Support\ServiceProvider;
use Sprout\Propagator\Categories\OverridesCategory;
use Sprout\Propagator\Categories\ProvidersCategory;
use Sprout\Propagator\Categories\ResolversCategory;
use Sprout\Propagator\Categories\TenanciesCategory;
use Sprout\Propagator\Commands\SproutConfigCommand;
use Sprout\Propagator\Contracts\CategoryRegistry;
use Sprout\Propagator\Contracts\WizardRenderer;
use Sprout\Propagator\Renderers\CliWizardRenderer;
use Sprout\Propagator\Support\ConfigWriter;
use Sprout\Propagator\Support\DefaultCategoryRegistry;

/**
 * Propagator Service Provider
 *
 * Registers all Propagator bindings, commands, and built-in config
 * categories with the Laravel application.
 *
 * @package Core
 */
class PropagatorServiceProvider extends ServiceProvider
{
    /**
     * Register any application services
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../resources/config/propagator.php', 'propagator');

        $this->app->singleton(CategoryRegistry::class, DefaultCategoryRegistry::class);
        $this->app->singleton(WizardRenderer::class, CliWizardRenderer::class);
        $this->app->singleton(ConfigWriter::class);
    }

    /**
     * Bootstrap any application services
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerCategories();
        $this->registerCommands();
        $this->registerPublishing();
    }

    /**
     * Register the built-in config categories
     *
     * @return void
     */
    private function registerCategories(): void
    {
        /** @var \Sprout\Propagator\Contracts\CategoryRegistry $registry */
        $registry = $this->app->make(CategoryRegistry::class);

        $registry->register(new ResolversCategory());
        $registry->register(new ProvidersCategory());
        $registry->register(new TenanciesCategory());
        $registry->register(new OverridesCategory());
    }

    /**
     * Register the Artisan commands
     *
     * @return void
     */
    private function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                SproutConfigCommand::class,
            ]);
        }
    }

    /**
     * Register the publishable assets
     *
     * @return void
     */
    private function registerPublishing(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../resources/config/propagator.php' => config_path('propagator.php'),
            ], 'propagator-config');
        }
    }
}
