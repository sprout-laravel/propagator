<?php
declare(strict_types=1);

namespace Sprout\Propagator\Fields;

/**
 * Text Field
 *
 * A field that collects free text input.
 *
 * @package Fields
 */
final class TextField extends BaseField
{
    /**
     * Create a new text field
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
        return 'text';
    }
}
