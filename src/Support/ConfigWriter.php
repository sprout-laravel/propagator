<?php
declare(strict_types=1);

namespace Sprout\Propagator\Support;

use Sprout\Propagator\Values\ClassReference;
use Sprout\Propagator\Values\EnvValue;
use Sprout\Propagator\Values\MethodCall;

/**
 * Config Writer
 *
 * Serialises a config array to a PHP file string. Handles value objects
 * (EnvValue, ClassReference, MethodCall) alongside scalars and nested arrays.
 */
final class ConfigWriter
{
    /**
     * Render a config array as a complete PHP config file string
     *
     * @param array<string, mixed> $config
     *
     * @return string
     */
    public function render(array $config): string
    {
        $body = $this->renderArray($config, 1);

        return "<?php\n\nreturn [\n{$body}];\n";
    }

    /**
     * Render an array of config values with indentation
     *
     * @param array<array-key, mixed> $array
     * @param int                     $depth
     *
     * @return string
     */
    private function renderArray(array $array, int $depth): string
    {
        $indent       = str_repeat('    ', $depth);
        $output       = '';
        $isSequential = array_is_list($array);

        foreach ($array as $key => $value) {
            if ($isSequential) {
                $output .= $indent . $this->renderValue($value, $depth) . ",\n";
            } else {
                $output .= $indent . "'" . $key . "' => " . $this->renderValue($value, $depth) . ",\n";
            }
        }

        return $output;
    }

    /**
     * Render a single value
     *
     * @param mixed $value
     * @param int   $depth
     *
     * @return string
     */
    private function renderValue(mixed $value, int $depth): string
    {
        if ($value instanceof EnvValue) {
            return $this->renderEnvValue($value, $depth);
        }

        if ($value instanceof ClassReference) {
            return '\\' . $value->fqcn . '::class';
        }

        if ($value instanceof MethodCall) {
            return $this->renderMethodCall($value);
        }

        if (is_array($value)) {
            if (empty($value)) {
                return '[]';
            }

            $closingIndent = str_repeat('    ', $depth);

            return "[\n" . $this->renderArray($value, $depth + 1) . $closingIndent . ']';
        }

        if (is_string($value)) {
            return "'" . addslashes($value) . "'";
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_null($value)) {
            return 'null';
        }

        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        return var_export($value, true);
    }

    /**
     * Render an EnvValue, handling recursive fallbacks
     *
     * @param EnvValue $value
     * @param int      $depth
     *
     * @return string
     */
    private function renderEnvValue(EnvValue $value, int $depth): string
    {
        if ($value->fallback === null) {
            return "env('" . $value->key . "')";
        }

        $fallback = $this->renderValue($value->fallback, $depth);

        return "env('" . $value->key . "', " . $fallback . ')';
    }

    /**
     * Render a MethodCall value
     *
     * @param MethodCall $value
     *
     * @return string
     */
    private function renderMethodCall(MethodCall $value): string
    {
        $args = '';

        if (! empty($value->arguments)) {
            $rendered = array_map(fn (mixed $arg) => $this->renderValue($arg, 0), $value->arguments);
            $args     = implode(', ', $rendered);
        }

        return '\\' . $value->fqcn . '::' . $value->method . '(' . $args . ')';
    }
}
