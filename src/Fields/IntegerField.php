<?php
declare(strict_types=1);

namespace Sprout\Propagator\Fields;

/**
 * Integer Field
 *
 * A field that collects numeric integer input.
 */
final class IntegerField extends BaseField
{
    /**
     * Create a new integer field
     *
     * @param string $name
     *
     * @return self
     */
    public static function make(string $name): self
    {
        return new self($name);
    }

    /**
     * Get the type identifier for this field
     *
     * @return string
     */
    public function getType(): string
    {
        return 'integer';
    }
}
