<?php
declare(strict_types=1);

namespace Sprout\Propagator\Tests\Unit\Support;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sprout\Propagator\Support\ConfigWriter;
use Sprout\Propagator\Values\ClassReference;
use Sprout\Propagator\Values\EnvValue;
use Sprout\Propagator\Values\MethodCall;

#[CoversClass(ConfigWriter::class)]
final class ConfigWriterTest extends TestCase
{
    private ConfigWriter $writer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->writer = new ConfigWriter();
    }

    #[Test]
    public function it_renders_scalar_values(): void
    {
        $config = [
            'key' => 'value',
            'number' => 42,
            'flag' => true,
            'nothing' => null,
        ];

        $output = $this->writer->render($config);

        $this->assertStringContainsString("'key' => 'value'", $output);
        $this->assertStringContainsString("'number' => 42", $output);
        $this->assertStringContainsString("'flag' => true", $output);
        $this->assertStringContainsString("'nothing' => null", $output);
    }

    #[Test]
    public function it_renders_env_values(): void
    {
        $config = [
            'domain' => new EnvValue('TENANTED_DOMAIN'),
        ];

        $output = $this->writer->render($config);

        $this->assertStringContainsString("'domain' => env('TENANTED_DOMAIN')", $output);
    }

    #[Test]
    public function it_renders_env_values_with_fallback(): void
    {
        $config = [
            'domain' => new EnvValue('TENANTED_DOMAIN', 'localhost'),
        ];

        $output = $this->writer->render($config);

        $this->assertStringContainsString("'domain' => env('TENANTED_DOMAIN', 'localhost')", $output);
    }

    #[Test]
    public function it_renders_nested_env_values(): void
    {
        $config = [
            'domain' => new EnvValue('TENANTED_DOMAIN', new EnvValue('APP_DOMAIN', 'localhost')),
        ];

        $output = $this->writer->render($config);

        $this->assertStringContainsString("'domain' => env('TENANTED_DOMAIN', env('APP_DOMAIN', 'localhost'))", $output);
    }

    #[Test]
    public function it_renders_class_references(): void
    {
        $config = [
            'model' => new ClassReference('App\\Models\\Tenant'),
        ];

        $output = $this->writer->render($config);

        $this->assertStringContainsString("'model' => \\App\\Models\\Tenant::class", $output);
    }

    #[Test]
    public function it_renders_method_calls(): void
    {
        $config = [
            'option' => new MethodCall('App\\TenancyOptions', 'allOverrides'),
        ];

        $output = $this->writer->render($config);

        $this->assertStringContainsString("\\App\\TenancyOptions::allOverrides()", $output);
    }

    #[Test]
    public function it_renders_nested_arrays(): void
    {
        $config = [
            'resolvers' => [
                'subdomain' => [
                    'driver' => 'subdomain',
                    'domain' => new EnvValue('TENANTED_DOMAIN'),
                ],
            ],
        ];

        $output = $this->writer->render($config);

        $this->assertStringContainsString("'resolvers' => [", $output);
        $this->assertStringContainsString("'subdomain' => [", $output);
        $this->assertStringContainsString("'driver' => 'subdomain'", $output);
    }

    #[Test]
    public function it_wraps_output_in_php_return_statement(): void
    {
        $config = ['key' => 'value'];

        $output = $this->writer->render($config);

        $this->assertStringStartsWith('<?php', $output);
        $this->assertStringContainsString('return [', $output);
        $this->assertStringEndsWith("];\n", $output);
    }

    #[Test]
    public function it_renders_sequential_arrays_without_keys(): void
    {
        $config = [
            'items' => [
                new MethodCall('App\\TenancyOptions', 'hydrateTenantRelation'),
                new MethodCall('App\\TenancyOptions', 'allOverrides'),
            ],
        ];

        $output = $this->writer->render($config);

        $this->assertStringContainsString("\\App\\TenancyOptions::hydrateTenantRelation()", $output);
        $this->assertStringContainsString("\\App\\TenancyOptions::allOverrides()", $output);
        $this->assertStringNotContainsString("'0' =>", $output);
    }

    #[Test]
    public function it_renders_float_values(): void
    {
        $config = ['ratio' => 3.14];

        $output = $this->writer->render($config);

        $this->assertStringContainsString("'ratio' => 3.14", $output);
    }

    #[Test]
    public function it_renders_empty_config(): void
    {
        $output = $this->writer->render([]);

        $this->assertSame("<?php\n\nreturn [\n];\n", $output);
    }

    #[Test]
    public function it_escapes_single_quotes_in_strings(): void
    {
        $config = ['message' => "it's a test"];

        $output = $this->writer->render($config);

        $this->assertStringContainsString("'message' => 'it\\'s a test'", $output);
    }

    #[Test]
    public function it_renders_method_calls_with_arguments(): void
    {
        $config = [
            'option' => new MethodCall('App\\Options', 'withValue', ['arg1']),
        ];

        $output = $this->writer->render($config);

        $this->assertStringContainsString("\\App\\Options::withValue('arg1')", $output);
    }

    #[Test]
    public function it_renders_empty_nested_arrays(): void
    {
        $config = ['items' => []];

        $output = $this->writer->render($config);

        $this->assertStringContainsString("'items' => []", $output);
    }

    #[Test]
    public function it_renders_deeply_nested_arrays(): void
    {
        $config = [
            'a' => [
                'b' => [
                    'c' => 'deep',
                ],
            ],
        ];

        $output = $this->writer->render($config);

        $this->assertStringContainsString("'a' => [", $output);
        $this->assertStringContainsString("'b' => [", $output);
        $this->assertStringContainsString("'c' => 'deep'", $output);
    }

    #[Test]
    public function it_renders_unknown_value_types_with_var_export(): void
    {
        $config = [
            'value' => new \stdClass(),
        ];

        $output = $this->writer->render($config);

        // var_export renders stdClass as (object) array(...)
        $this->assertStringContainsString("'value' =>", $output);
    }
}
