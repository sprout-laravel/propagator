<?php
declare(strict_types=1);

namespace Sprout\Propagator\Tests\Feature\Commands;

use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Sprout\Propagator\Commands\SproutConfigCommand;
use Sprout\Propagator\Contracts\CategoryRegistry;
use Sprout\Propagator\Contracts\DriverWizard;
use Sprout\Propagator\Contracts\WizardRenderer;
use Sprout\Propagator\Fields\FieldDependency;
use Sprout\Propagator\Fields\TextField;
use Sprout\Propagator\PropagatorServiceProvider;
use Sprout\Propagator\Tests\Support\MockWizardRenderer;
use Sprout\Propagator\Tests\Unit\UnitTestCase;

#[CoversClass(SproutConfigCommand::class)]
final class SproutConfigCommandTest extends UnitTestCase
{
    protected function getPackageProviders($app): array
    {
        return [PropagatorServiceProvider::class];
    }

    /**
     * Run the sprout:config command with a mock renderer and return output
     *
     * @param array<string, mixed>  $arguments
     * @param array<int, mixed>     $responses
     *
     * @return array{exitCode: int, output: string}
     */
    private function runCommand(array $arguments, array $responses): array
    {
        $this->app->instance(WizardRenderer::class, new MockWizardRenderer($responses));

        $exitCode = Artisan::call('sprout:config', $arguments);

        return [
            'exitCode' => $exitCode,
            'output'   => Artisan::output(),
        ];
    }

    /**
     * Evaluate a PHP config string (as output by manual mode) into an array
     *
     * @param string $php
     *
     * @return array<string, mixed>
     */
    private function evaluatePhpConfig(string $php): array
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'propagator_test_');
        file_put_contents($tempFile, trim($php));

        /** @var array<string, mixed> $config */
        $config = require $tempFile;

        unlink($tempFile);

        return $config;
    }

    /**
     * Load a written config file as an array
     *
     * @param string $path
     *
     * @return array<string, mixed>
     */
    private function loadWrittenConfig(string $path): array
    {
        /** @var array<string, mixed> $config */
        $config = require $path;

        return $config;
    }

    /**
     * Create a config file and return its path
     *
     * @param string $name
     * @param string $contents
     *
     * @return string
     */
    private function ensureConfigFile(string $name, string $contents): string
    {
        $path = config_path($name . '.php');
        $dir  = dirname($path);

        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($path, $contents);

        return $path;
    }

    // ──────────────────────────────────────────────────────────────────
    // Add — manual mode
    // ──────────────────────────────────────────────────────────────────

    #[Test]
    public function it_adds_a_resolver_in_manual_mode_and_outputs_valid_config(): void
    {
        $this->app['config']->set('propagator.mode', 'manual');

        $result = $this->runCommand(
            ['action' => 'add', 'category' => 'resolvers'],
            [
                'header',      // selectFromList: Which driver?
                'my-header',   // renderField: Entry name
                'X-Tenant',    // renderField: header field value
            ],
        );

        $this->assertSame(0, $result['exitCode']);

        $config = $this->evaluatePhpConfig($result['output']);

        $this->assertArrayHasKey('my-header', $config);
        $this->assertSame('header', $config['my-header']['driver']);
        $this->assertSame('X-Tenant', $config['my-header']['header']);
    }

    #[Test]
    public function it_adds_a_provider_in_manual_mode_and_outputs_valid_config(): void
    {
        $this->app['config']->set('propagator.mode', 'manual');

        $result = $this->runCommand(
            ['action' => 'add', 'category' => 'providers'],
            [
                'eloquent',
                'tenants',
                'App\\Models\\Tenant',
            ],
        );

        $this->assertSame(0, $result['exitCode']);

        $config = $this->evaluatePhpConfig($result['output']);

        $this->assertArrayHasKey('tenants', $config);
        $this->assertSame('eloquent', $config['tenants']['driver']);
        $this->assertSame('App\\Models\\Tenant', $config['tenants']['model']);
    }

    // ──────────────────────────────────────────────────────────────────
    // Add — managed mode
    // ──────────────────────────────────────────────────────────────────

    #[Test]
    public function it_adds_an_entry_in_managed_mode_and_writes_correct_config(): void
    {
        $this->app['config']->set('propagator.mode', 'managed');
        $this->app['config']->set('multitenancy', [
            'resolvers' => [
                'existing' => ['driver' => 'path', 'segment' => 1],
            ],
        ]);

        $configPath = $this->ensureConfigFile('multitenancy', "<?php\n\nreturn [];\n");

        $result = $this->runCommand(
            ['action' => 'add', 'category' => 'resolvers'],
            [
                'header',
                'api-header',
                'X-Tenant-ID',
            ],
        );

        $this->assertSame(0, $result['exitCode']);

        $config = $this->loadWrittenConfig($configPath);

        // New entry was added with correct values
        $this->assertArrayHasKey('resolvers', $config);
        $this->assertArrayHasKey('api-header', $config['resolvers']);
        $this->assertSame('header', $config['resolvers']['api-header']['driver']);
        $this->assertSame('X-Tenant-ID', $config['resolvers']['api-header']['header']);

        // Existing entry was preserved
        $this->assertArrayHasKey('existing', $config['resolvers']);
        $this->assertSame('path', $config['resolvers']['existing']['driver']);

        @unlink($configPath);
    }

    // ──────────────────────────────────────────────────────────────────
    // Edit
    // ──────────────────────────────────────────────────────────────────

    #[Test]
    public function it_edits_an_entry_and_outputs_snippet_with_new_values(): void
    {
        $this->app['config']->set('propagator.mode', 'manual');
        $this->app['config']->set('multitenancy.resolvers', [
            'my-header' => ['driver' => 'header', 'header' => 'X-Old'],
        ]);

        $result = $this->runCommand(
            ['action' => 'edit', 'category' => 'resolvers', 'name' => 'my-header'],
            ['X-New-Header'],
        );

        $this->assertSame(0, $result['exitCode']);

        $config = $this->evaluatePhpConfig($result['output']);

        $this->assertArrayHasKey('my-header', $config);
        $this->assertSame('header', $config['my-header']['driver']);
        $this->assertSame('X-New-Header', $config['my-header']['header']);
    }

    #[Test]
    public function it_edits_an_entry_in_managed_mode_and_writes_updated_config(): void
    {
        $this->app['config']->set('propagator.mode', 'managed');
        $this->app['config']->set('multitenancy', [
            'resolvers' => [
                'my-header' => ['driver' => 'header', 'header' => 'X-Old'],
            ],
        ]);

        $configPath = $this->ensureConfigFile('multitenancy', "<?php\n\nreturn [];\n");

        $result = $this->runCommand(
            ['action' => 'edit', 'category' => 'resolvers', 'name' => 'my-header'],
            ['X-Updated'],
        );

        $this->assertSame(0, $result['exitCode']);

        $config = $this->loadWrittenConfig($configPath);

        $this->assertSame('X-Updated', $config['resolvers']['my-header']['header']);
        $this->assertSame('header', $config['resolvers']['my-header']['driver']);

        @unlink($configPath);
    }

    #[Test]
    public function it_fails_when_editing_nonexistent_entry(): void
    {
        $this->app['config']->set('multitenancy.resolvers', []);

        $result = $this->runCommand(
            ['action' => 'edit', 'category' => 'resolvers', 'name' => 'nonexistent'],
            [],
        );

        $this->assertSame(1, $result['exitCode']);
    }

    #[Test]
    public function it_prompts_for_entry_name_on_edit_when_not_provided(): void
    {
        $this->app['config']->set('propagator.mode', 'manual');
        $this->app['config']->set('multitenancy.resolvers', [
            'subdomain' => ['driver' => 'subdomain', 'domain' => 'example.com', 'pattern' => '.*'],
        ]);

        $result = $this->runCommand(
            ['action' => 'edit', 'category' => 'resolvers'],
            [
                'subdomain',   // selectFromList: Which entry?
                'MY_DOMAIN',   // renderField: domain (EnvField)
                '.*',          // renderField: pattern
            ],
        );

        $this->assertSame(0, $result['exitCode']);

        $config = $this->evaluatePhpConfig($result['output']);

        $this->assertArrayHasKey('subdomain', $config);
        $this->assertSame('subdomain', $config['subdomain']['driver']);
    }

    // ──────────────────────────────────────────────────────────────────
    // Delete — manual mode
    // ──────────────────────────────────────────────────────────────────

    #[Test]
    public function it_deletes_in_manual_mode_and_instructs_user_to_remove_entry(): void
    {
        $this->app['config']->set('propagator.mode', 'manual');
        $this->app['config']->set('multitenancy.resolvers', [
            'old-resolver' => ['driver' => 'header', 'header' => 'X-Old'],
        ]);

        $result = $this->runCommand(
            ['action' => 'delete', 'category' => 'resolvers', 'name' => 'old-resolver'],
            [true],
        );

        $this->assertSame(0, $result['exitCode']);
        // The message tells the user which entry to remove
        $this->assertStringContainsString("Remove the 'old-resolver' entry from your config file", $result['output']);
    }

    #[Test]
    public function it_cancels_delete_when_not_confirmed(): void
    {
        $this->app['config']->set('multitenancy.resolvers', [
            'keep' => ['driver' => 'header'],
        ]);

        $result = $this->runCommand(
            ['action' => 'delete', 'category' => 'resolvers', 'name' => 'keep'],
            [false],
        );

        $this->assertSame(0, $result['exitCode']);
        $this->assertStringContainsString('Cancelled', $result['output']);
    }

    #[Test]
    public function it_fails_when_deleting_nonexistent_entry(): void
    {
        $this->app['config']->set('multitenancy.resolvers', []);

        $result = $this->runCommand(
            ['action' => 'delete', 'category' => 'resolvers', 'name' => 'nonexistent'],
            [],
        );

        $this->assertSame(1, $result['exitCode']);
    }

    // ──────────────────────────────────────────────────────────────────
    // Delete — managed mode
    // ──────────────────────────────────────────────────────────────────

    #[Test]
    public function it_deletes_in_managed_mode_removing_only_the_target_entry(): void
    {
        $this->app['config']->set('propagator.mode', 'managed');
        $this->app['config']->set('multitenancy', [
            'resolvers' => [
                'to-delete' => ['driver' => 'header', 'header' => 'X-Old'],
                'to-keep'   => ['driver' => 'path', 'segment' => 1],
            ],
        ]);

        $configPath = $this->ensureConfigFile('multitenancy', "<?php\n\nreturn [];\n");

        $result = $this->runCommand(
            ['action' => 'delete', 'category' => 'resolvers', 'name' => 'to-delete'],
            [true],
        );

        $this->assertSame(0, $result['exitCode']);

        $config = $this->loadWrittenConfig($configPath);

        // Deleted entry is gone
        $this->assertArrayNotHasKey('to-delete', $config['resolvers']);

        // Other entry is preserved
        $this->assertArrayHasKey('to-keep', $config['resolvers']);
        $this->assertSame('path', $config['resolvers']['to-keep']['driver']);
        $this->assertSame(1, $config['resolvers']['to-keep']['segment']);

        @unlink($configPath);
    }

    // ──────────────────────────────────────────────────────────────────
    // Field dependency skipping
    // ──────────────────────────────────────────────────────────────────

    #[Test]
    public function it_skips_fields_with_unmet_dependencies(): void
    {
        $this->app['config']->set('propagator.mode', 'manual');

        // Create a driver wizard with a dependent field
        $wizard = new class implements DriverWizard {
            public function getName(): string { return 'test-driver'; }
            public function getLabel(): string { return 'Test Driver'; }
            public function getFields(): array
            {
                return [
                    TextField::make('mode')->label('Mode'),
                    // This field depends on mode being 'advanced' — it will be skipped
                    TextField::make('advanced_option')
                        ->label('Advanced Option')
                        ->dependsOn(FieldDependency::when('mode', 'advanced')),
                ];
            }
        };

        // Register it on the resolvers category
        $registry = $this->app->make(CategoryRegistry::class);
        $registry->get('resolvers')->drivers()->add($wizard);

        $result = $this->runCommand(
            ['action' => 'add', 'category' => 'resolvers'],
            [
                'test-driver',   // selectFromList: Which driver?
                'test-entry',    // renderField: Entry name
                'basic',         // renderField: mode (not 'advanced', so next field skipped)
                // NO response for advanced_option — it should be skipped
            ],
        );

        $this->assertSame(0, $result['exitCode']);

        $config = $this->evaluatePhpConfig($result['output']);

        $this->assertArrayHasKey('test-entry', $config);
        $this->assertSame('basic', $config['test-entry']['mode']);
        $this->assertArrayNotHasKey('advanced_option', $config['test-entry']);
    }

    // ──────────────────────────────────────────────────────────────────
    // Unknown action
    // ──────────────────────────────────────────────────────────────────

    #[Test]
    public function it_returns_failure_for_unknown_action(): void
    {
        $result = $this->runCommand(
            ['action' => 'invalid', 'category' => 'resolvers'],
            [],
        );

        $this->assertSame(1, $result['exitCode']);
    }

    // ──────────────────────────────────────────────────────────────────
    // Interactive prompting (no arguments)
    // ──────────────────────────────────────────────────────────────────

    #[Test]
    public function it_prompts_for_action_and_category_when_not_provided(): void
    {
        $this->app['config']->set('propagator.mode', 'manual');

        $result = $this->runCommand(
            [],
            [
                'add',         // selectFromList: What would you like to do?
                'resolvers',   // selectFromList: What would you like to configure?
                'header',      // selectFromList: Which driver?
                'my-header',   // renderField: Entry name
                'X-Tenant',    // renderField: header value
            ],
        );

        $this->assertSame(0, $result['exitCode']);

        $config = $this->evaluatePhpConfig($result['output']);

        $this->assertArrayHasKey('my-header', $config);
        $this->assertSame('header', $config['my-header']['driver']);
        $this->assertSame('X-Tenant', $config['my-header']['header']);
    }

    // ──────────────────────────────────────────────────────────────────
    // Overrides (empty config key path)
    // ──────────────────────────────────────────────────────────────────

    #[Test]
    public function it_writes_overrides_config_at_root_level_not_nested(): void
    {
        $this->app['config']->set('propagator.mode', 'managed');
        $this->app['config']->set('overrides', [
            'cache'   => ['driver' => 'App\\Override\\Cache'],
            'session' => ['driver' => 'App\\Override\\Session'],
        ]);

        $configPath = $this->ensureConfigFile('overrides', "<?php\n\nreturn [];\n");

        $result = $this->runCommand(
            ['action' => 'delete', 'category' => 'overrides', 'name' => 'cache'],
            [true],
        );

        $this->assertSame(0, $result['exitCode']);

        $config = $this->loadWrittenConfig($configPath);

        // Deleted entry is gone
        $this->assertArrayNotHasKey('cache', $config);

        // Other entry is preserved at root level (not nested under a key)
        $this->assertArrayHasKey('session', $config);
        $this->assertSame('App\\Override\\Session', $config['session']['driver']);

        @unlink($configPath);
    }
}
