<?php
declare(strict_types=1);

namespace Sprout\Propagator\Values;

/**
 * Env Value
 *
 * Represents an env() call in a config file. Supports recursive fallbacks
 * where the fallback can itself be an EnvValue.
 */
final class EnvValue
{
    /**
     * Create a new instance
     *
     * @param string $key
     * @param mixed  $fallback
     */
    public function __construct(
        public readonly string $key,
        public readonly mixed  $fallback = null,
    ) {
    }
}
