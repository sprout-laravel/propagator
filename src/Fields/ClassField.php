<?php
declare(strict_types=1);

namespace Sprout\Propagator\Fields;

/**
 * Class Field
 *
 * A field that collects a fully qualified class name.
 *
 * @package Fields
 */
final class ClassField extends BaseField
{
    /**
     * Create a new class field
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
        return 'class';
    }
}
