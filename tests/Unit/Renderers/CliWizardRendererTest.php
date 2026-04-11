<?php
declare(strict_types=1);

namespace Sprout\Propagator\Tests\Unit\Renderers;

use Laravel\Prompts\Prompt;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Sprout\Propagator\Contracts\WizardRenderer;
use Sprout\Propagator\PropagatorServiceProvider;
use Sprout\Propagator\Tests\Unit\UnitTestCase;
use Sprout\Propagator\Fields\ArrayField;
use Sprout\Propagator\Fields\BooleanField;
use Sprout\Propagator\Fields\ClassField;
use Sprout\Propagator\Fields\EnvField;
use Sprout\Propagator\Fields\GroupField;
use Sprout\Propagator\Fields\IntegerField;
use Sprout\Propagator\Fields\SelectField;
use Sprout\Propagator\Fields\TextField;
use Sprout\Propagator\Renderers\CliWizardRenderer;
use Sprout\Propagator\Values\EnvValue;

#[CoversClass(CliWizardRenderer::class)]
final class CliWizardRendererTest extends UnitTestCase
{
    protected function getPackageProviders($app): array
    {
        return [PropagatorServiceProvider::class];
    }

    private CliWizardRenderer $renderer;

    protected function setUp(): void
    {
        parent::setUp();

        // Force prompts into non-interactive mode so they return default values
        // without requiring a real terminal
        Prompt::interactive(false);

        $this->renderer = new CliWizardRenderer();
    }

    protected function tearDown(): void
    {
        // Re-enable interactive mode for other tests
        Prompt::interactive();

        parent::tearDown();
    }

    // ---------------------------------------------------------------
    // Interface / contract
    // ---------------------------------------------------------------

    #[Test]
    public function it_implements_wizard_renderer(): void
    {
        $this->assertInstanceOf(WizardRenderer::class, $this->renderer);
    }

    // ---------------------------------------------------------------
    // renderField() dispatch — text type
    // ---------------------------------------------------------------

    #[Test]
    public function render_field_dispatches_text_type_to_render_text_field(): void
    {
        $field = TextField::make('name')
            ->label('Name')
            ->default('default-value');

        $result = $this->renderer->renderField($field);

        $this->assertSame('default-value', $result);
    }

    // ---------------------------------------------------------------
    // renderField() dispatch — integer type
    // ---------------------------------------------------------------

    #[Test]
    public function render_field_dispatches_integer_type_to_render_text_field(): void
    {
        $field = IntegerField::make('port')
            ->label('Port')
            ->default('8080');

        $result = $this->renderer->renderField($field);

        $this->assertSame('8080', $result);
    }

    // ---------------------------------------------------------------
    // renderField() dispatch — class type
    // ---------------------------------------------------------------

    #[Test]
    public function render_field_dispatches_class_type_to_render_text_field(): void
    {
        $field = ClassField::make('model')
            ->label('Model')
            ->default('App\\Models\\Tenant');

        $result = $this->renderer->renderField($field);

        $this->assertSame('App\\Models\\Tenant', $result);
    }

    // ---------------------------------------------------------------
    // renderField() dispatch — boolean type
    // ---------------------------------------------------------------

    #[Test]
    public function render_field_dispatches_boolean_type_to_render_boolean_field(): void
    {
        $field = BooleanField::make('enabled')
            ->label('Enable feature?')
            ->default(true);

        $result = $this->renderer->renderField($field);

        $this->assertTrue($result);
    }

    // ---------------------------------------------------------------
    // renderField() dispatch — select type
    // ---------------------------------------------------------------

    #[Test]
    public function render_field_dispatches_select_type_to_render_select_field(): void
    {
        $field = SelectField::make('driver')
            ->label('Driver')
            ->options(['mysql' => 'MySQL', 'pgsql' => 'PostgreSQL'])
            ->default('pgsql');

        $result = $this->renderer->renderField($field);

        $this->assertSame('pgsql', $result);
    }

    // ---------------------------------------------------------------
    // renderField() dispatch — env type
    // ---------------------------------------------------------------

    #[Test]
    public function render_field_dispatches_env_type_to_render_env_field(): void
    {
        $field = EnvField::make('db_connection')
            ->label('DB Connection')
            ->envKey('DB_CONNECTION');

        $result = $this->renderer->renderField($field);

        $this->assertInstanceOf(EnvValue::class, $result);
        $this->assertSame('DB_CONNECTION', $result->key);
    }

    // ---------------------------------------------------------------
    // renderField() dispatch — group type
    // ---------------------------------------------------------------

    #[Test]
    public function render_field_dispatches_group_type_to_render_group_field(): void
    {
        $field = GroupField::make('database')
            ->label('Database Settings')
            ->fields([
                TextField::make('host')->label('Host')->default('localhost'),
                TextField::make('port')->label('Port')->default('3306'),
            ]);

        $result = $this->renderer->renderField($field);

        $this->assertIsArray($result);
        $this->assertSame(['host' => 'localhost', 'port' => '3306'], $result);
    }

    // ---------------------------------------------------------------
    // renderField() dispatch — array type
    // ---------------------------------------------------------------

    #[Test]
    public function render_field_dispatches_array_type_to_render_array_field(): void
    {
        // In non-interactive mode, text() returns '' immediately, so the
        // loop breaks on the first iteration and returns the starting items
        $field = ArrayField::make('tags')
            ->label('Tags');

        $result = $this->renderer->renderField($field);

        $this->assertIsArray($result);
        $this->assertSame([], $result);
    }

    // ---------------------------------------------------------------
    // renderField() dispatch — unknown type falls back to text
    // ---------------------------------------------------------------

    #[Test]
    public function render_field_dispatches_unknown_type_to_render_text_field(): void
    {
        // Create an anonymous class with an unknown type
        $field = new class ('custom') extends \Sprout\Propagator\Fields\BaseField {
            public static function make(string $name): static
            {
                return new static($name);
            }

            public function getType(): string
            {
                return 'unknown_type';
            }
        };

        $field->label('Custom field')->default('fallback-default');

        $result = $this->renderer->renderField($field);

        $this->assertSame('fallback-default', $result);
    }

    // ---------------------------------------------------------------
    // renderTextField()
    // ---------------------------------------------------------------

    #[Test]
    public function render_text_field_uses_field_default_when_no_current_value(): void
    {
        $field = TextField::make('host')
            ->label('Hostname')
            ->default('localhost');

        $result = $this->renderer->renderField($field);

        $this->assertSame('localhost', $result);
    }

    #[Test]
    public function render_text_field_uses_current_value_over_default(): void
    {
        $field = TextField::make('host')
            ->label('Hostname')
            ->default('localhost');

        $result = $this->renderer->renderField($field, 'custom-host');

        $this->assertSame('custom-host', $result);
    }

    #[Test]
    public function render_text_field_returns_empty_string_when_no_default_and_no_current_value(): void
    {
        $field = TextField::make('host')
            ->label('Hostname');

        $result = $this->renderer->renderField($field);

        $this->assertSame('', $result);
    }

    #[Test]
    public function render_text_field_casts_non_scalar_default_to_empty_string(): void
    {
        // When the default is an array (non-scalar), it should become ''
        $field = TextField::make('host')
            ->label('Hostname')
            ->default(['not', 'scalar']);

        $result = $this->renderer->renderField($field);

        $this->assertSame('', $result);
    }

    #[Test]
    public function render_text_field_casts_integer_default_to_string(): void
    {
        $field = IntegerField::make('count')
            ->label('Count')
            ->default(42);

        $result = $this->renderer->renderField($field);

        $this->assertSame('42', $result);
    }

    #[Test]
    public function render_text_field_casts_boolean_current_value_to_string(): void
    {
        $field = TextField::make('flag')
            ->label('Flag');

        $result = $this->renderer->renderField($field, true);

        $this->assertSame('1', $result);
    }

    // ---------------------------------------------------------------
    // renderBooleanField()
    // ---------------------------------------------------------------

    #[Test]
    public function render_boolean_field_returns_true_when_default_is_true(): void
    {
        $field = BooleanField::make('enabled')
            ->label('Enable?')
            ->default(true);

        $result = $this->renderer->renderField($field);

        $this->assertTrue($result);
    }

    #[Test]
    public function render_boolean_field_returns_false_when_default_is_false(): void
    {
        $field = BooleanField::make('enabled')
            ->label('Enable?')
            ->default(false);

        $result = $this->renderer->renderField($field);

        $this->assertFalse($result);
    }

    #[Test]
    public function render_boolean_field_uses_current_value_over_default(): void
    {
        $field = BooleanField::make('enabled')
            ->label('Enable?')
            ->default(false);

        $result = $this->renderer->renderField($field, true);

        $this->assertTrue($result);
    }

    #[Test]
    public function render_boolean_field_defaults_to_false_when_no_default_or_current_value(): void
    {
        $field = BooleanField::make('enabled')
            ->label('Enable?');

        $result = $this->renderer->renderField($field);

        $this->assertFalse($result);
    }

    #[Test]
    public function render_boolean_field_casts_truthy_current_value(): void
    {
        $field = BooleanField::make('enabled')
            ->label('Enable?');

        // A truthy integer should be cast to true
        $result = $this->renderer->renderField($field, 1);

        $this->assertTrue($result);
    }

    // ---------------------------------------------------------------
    // renderSelectField()
    // ---------------------------------------------------------------

    #[Test]
    public function render_select_field_returns_default_value(): void
    {
        $field = SelectField::make('driver')
            ->label('Driver')
            ->options(['mysql' => 'MySQL', 'pgsql' => 'PostgreSQL', 'sqlite' => 'SQLite'])
            ->default('sqlite');

        $result = $this->renderer->renderField($field);

        $this->assertSame('sqlite', $result);
    }

    #[Test]
    public function render_select_field_uses_current_value_over_default(): void
    {
        $field = SelectField::make('driver')
            ->label('Driver')
            ->options(['mysql' => 'MySQL', 'pgsql' => 'PostgreSQL'])
            ->default('mysql');

        $result = $this->renderer->renderField($field, 'pgsql');

        $this->assertSame('pgsql', $result);
    }

    #[Test]
    public function render_select_field_with_integer_default(): void
    {
        $field = SelectField::make('level')
            ->label('Level')
            ->options([1 => 'Low', 2 => 'Medium', 3 => 'High'])
            ->default(2);

        $result = $this->renderer->renderField($field);

        $this->assertSame('2', $result);
    }

    // ---------------------------------------------------------------
    // renderEnvField()
    // ---------------------------------------------------------------

    #[Test]
    public function render_env_field_without_fallback_returns_env_value_with_null_fallback(): void
    {
        $field = EnvField::make('app_key')
            ->label('Application Key')
            ->envKey('APP_KEY');

        $result = $this->renderer->renderField($field);

        $this->assertInstanceOf(EnvValue::class, $result);
        $this->assertSame('APP_KEY', $result->key);
        $this->assertNull($result->fallback);
    }

    #[Test]
    public function render_env_field_with_text_fallback_returns_env_value_with_fallback(): void
    {
        $fallbackField = TextField::make('fallback')
            ->label('Fallback')
            ->default('default-connection');

        $field = EnvField::make('db_connection')
            ->label('DB Connection')
            ->envKey('DB_CONNECTION')
            ->fallback($fallbackField);

        $result = $this->renderer->renderField($field);

        $this->assertInstanceOf(EnvValue::class, $result);
        $this->assertSame('DB_CONNECTION', $result->key);
        $this->assertSame('default-connection', $result->fallback);
    }

    #[Test]
    public function render_env_field_with_env_value_current_value_passes_fallback_to_nested_field(): void
    {
        $fallbackField = TextField::make('fallback')
            ->label('Fallback')
            ->default('original-default');

        $field = EnvField::make('db_connection')
            ->label('DB Connection')
            ->envKey('DB_CONNECTION')
            ->fallback($fallbackField);

        // When current value is an EnvValue, its fallback is used as
        // the current value for the nested fallback field
        $currentValue = new EnvValue('OLD_KEY', 'current-fallback-value');

        $result = $this->renderer->renderField($field, $currentValue);

        $this->assertInstanceOf(EnvValue::class, $result);
        // The env key comes from the field's envKey default, not the current value
        $this->assertSame('DB_CONNECTION', $result->key);
        // The fallback field gets the current EnvValue's fallback as its current value
        $this->assertSame('current-fallback-value', $result->fallback);
    }

    #[Test]
    public function render_env_field_without_env_key_returns_empty_string_key(): void
    {
        $field = EnvField::make('custom')
            ->label('Custom Env');

        $result = $this->renderer->renderField($field);

        $this->assertInstanceOf(EnvValue::class, $result);
        $this->assertSame('', $result->key);
        $this->assertNull($result->fallback);
    }

    #[Test]
    public function render_env_field_with_non_env_value_current_value_passes_null_to_fallback(): void
    {
        $fallbackField = TextField::make('fallback')
            ->label('Fallback')
            ->default('the-default');

        $field = EnvField::make('db_host')
            ->label('DB Host')
            ->envKey('DB_HOST')
            ->fallback($fallbackField);

        // When current value is not an EnvValue, null is passed as the
        // current fallback value
        $result = $this->renderer->renderField($field, 'plain-string');

        $this->assertInstanceOf(EnvValue::class, $result);
        // Fallback field gets null as current value, so uses its own default
        $this->assertSame('the-default', $result->fallback);
    }

    // ---------------------------------------------------------------
    // renderGroupField()
    // ---------------------------------------------------------------

    #[Test]
    public function render_group_field_renders_all_nested_fields(): void
    {
        $field = GroupField::make('database')
            ->label('Database')
            ->fields([
                TextField::make('host')->label('Host')->default('127.0.0.1'),
                TextField::make('port')->label('Port')->default('5432'),
                TextField::make('database')->label('Database')->default('app'),
            ]);

        $result = $this->renderer->renderField($field);

        $this->assertSame([
            'host'     => '127.0.0.1',
            'port'     => '5432',
            'database' => 'app',
        ], $result);
    }

    #[Test]
    public function render_group_field_uses_current_values_for_nested_fields(): void
    {
        $field = GroupField::make('database')
            ->label('Database')
            ->fields([
                TextField::make('host')->label('Host')->default('127.0.0.1'),
                TextField::make('port')->label('Port')->default('5432'),
            ]);

        $currentValues = [
            'host' => 'custom-host',
            'port' => '3306',
        ];

        $result = $this->renderer->renderField($field, $currentValues);

        $this->assertSame([
            'host' => 'custom-host',
            'port' => '3306',
        ], $result);
    }

    #[Test]
    public function render_group_field_handles_partial_current_values(): void
    {
        $field = GroupField::make('database')
            ->label('Database')
            ->fields([
                TextField::make('host')->label('Host')->default('localhost'),
                TextField::make('port')->label('Port')->default('3306'),
                TextField::make('name')->label('DB Name')->default('forge'),
            ]);

        // Only 'host' has a current value; others should use their defaults
        $currentValues = [
            'host' => 'production-host',
        ];

        $result = $this->renderer->renderField($field, $currentValues);

        $this->assertSame([
            'host' => 'production-host',
            'port' => '3306',
            'name' => 'forge',
        ], $result);
    }

    #[Test]
    public function render_group_field_treats_non_array_current_value_as_empty(): void
    {
        $field = GroupField::make('settings')
            ->label('Settings')
            ->fields([
                TextField::make('key')->label('Key')->default('value'),
            ]);

        // Non-array current value is treated as empty
        $result = $this->renderer->renderField($field, 'not-an-array');

        $this->assertSame(['key' => 'value'], $result);
    }

    #[Test]
    public function render_group_field_with_mixed_field_types(): void
    {
        $field = GroupField::make('config')
            ->label('Configuration')
            ->fields([
                TextField::make('name')->label('Name')->default('my-app'),
                BooleanField::make('debug')->label('Debug?')->default(true),
            ]);

        $result = $this->renderer->renderField($field);

        $this->assertSame('my-app', $result['name']);
        $this->assertTrue($result['debug']);
    }

    // ---------------------------------------------------------------
    // renderArrayField()
    // ---------------------------------------------------------------

    #[Test]
    public function render_array_field_returns_empty_array_when_first_input_is_empty(): void
    {
        // In non-interactive mode, text() returns '' immediately, breaking the loop
        $field = ArrayField::make('items')
            ->label('Items');

        $result = $this->renderer->renderField($field);

        $this->assertSame([], $result);
    }

    #[Test]
    public function render_array_field_preserves_current_value_items(): void
    {
        $field = ArrayField::make('items')
            ->label('Items');

        // Current value items are kept; the loop then gets '' and breaks
        $result = $this->renderer->renderField($field, ['existing-one', 'existing-two']);

        $this->assertSame(['existing-one', 'existing-two'], $result);
    }

    #[Test]
    public function render_array_field_uses_default_when_no_current_value(): void
    {
        $field = ArrayField::make('items')
            ->label('Items')
            ->default(['alpha', 'beta']);

        $result = $this->renderer->renderField($field);

        // Default items are loaded, then loop breaks on '' input
        $this->assertSame(['alpha', 'beta'], $result);
    }

    #[Test]
    public function render_array_field_treats_non_array_current_value_as_falling_back_to_default(): void
    {
        $field = ArrayField::make('items')
            ->label('Items')
            ->default(['from-default']);

        $result = $this->renderer->renderField($field, 'not-an-array');

        $this->assertSame(['from-default'], $result);
    }

    #[Test]
    public function render_array_field_treats_non_array_default_as_empty(): void
    {
        $field = ArrayField::make('items')
            ->label('Items')
            ->default('not-an-array');

        $result = $this->renderer->renderField($field);

        // Non-array default is passed through the is_array check and becomes []
        $this->assertSame([], $result);
    }

    // ---------------------------------------------------------------
    // confirm()
    // ---------------------------------------------------------------

    #[Test]
    public function confirm_returns_bool(): void
    {
        $result = $this->renderer->confirm('Are you sure?');

        $this->assertIsBool($result);
    }

    #[Test]
    public function confirm_returns_true_by_default(): void
    {
        // The confirm() function defaults to true in laravel/prompts
        $result = $this->renderer->confirm('Proceed?');

        $this->assertTrue($result);
    }

}
