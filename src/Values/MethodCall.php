<?php
declare(strict_types=1);

namespace Sprout\Propagator\Values;

/**
 * Method Call
 *
 * Represents a static method call in a config file,
 * e.g. TenancyOptions::allOverrides().
 */
final class MethodCall
{
    /**
     * Create a new instance
     *
     * @param class-string      $fqcn
     * @param string            $method
     * @param array<int, mixed> $arguments
     */
    public function __construct(
        public readonly string $fqcn,
        public readonly string $method,
        public readonly array  $arguments = [],
    ) {
    }
}
