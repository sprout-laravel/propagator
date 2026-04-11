<?php
declare(strict_types=1);

namespace Sprout\Propagator\Fields;

/**
 * Boolean Field
 *
 * A field that collects a yes/no boolean value.
 *
 * @package Fields
 */
final class BooleanField extends BaseField
{
    /**
     * Create a new boolean field
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
        return 'boolean';
    }
}
