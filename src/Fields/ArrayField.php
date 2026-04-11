<?php
declare(strict_types=1);

namespace Sprout\Propagator\Fields;

/**
 * Array Field
 *
 * A field that collects multiple values as an array.
 *
 * @package Fields
 */
final class ArrayField extends BaseField
{
    /**
     * Create a new array field
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
        return 'array';
    }
}
