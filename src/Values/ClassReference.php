<?php
declare(strict_types=1);

namespace Sprout\Propagator\Values;

/**
 * Class Reference
 *
 * Represents a ::class constant reference in a config file.
 *
 * @package Values
 */
final class ClassReference
{
    /**
     * Create a new instance
     *
     * @param class-string $fqcn
     */
    public function __construct(
        public readonly string $fqcn,
    )
    {
    }
}
