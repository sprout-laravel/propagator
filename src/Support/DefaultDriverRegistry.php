<?php
declare(strict_types=1);

namespace Sprout\Propagator\Support;

use InvalidArgumentException;
use Sprout\Propagator\Contracts\DriverRegistry;
use Sprout\Propagator\Contracts\DriverWizard;

/**
 * Default Driver Registry
 *
 * The default implementation of the driver registry, storing driver wizards
 * in an array keyed by name.
 */
final class DefaultDriverRegistry implements DriverRegistry
{
    /**
     * The registered driver wizards
     *
     * @var array<string, DriverWizard>
     */
    private array $drivers = [];

    /**
     * Add a driver wizard to the registry
     *
     * @param DriverWizard $driver
     *
     * @return void
     */
    public function add(DriverWizard $driver): void
    {
        $this->drivers[$driver->getName()] = $driver;
    }

    /**
     * Get a driver wizard by name
     *
     * @param string $name
     *
     * @return DriverWizard
     *
     * @throws InvalidArgumentException
     */
    public function get(string $name): DriverWizard
    {
        if (! $this->has($name)) {
            throw new InvalidArgumentException("Driver wizard [{$name}] is not registered.");
        }

        return $this->drivers[$name];
    }

    /**
     * Get all registered driver wizards
     *
     * @return array<string, DriverWizard>
     */
    public function all(): array
    {
        return $this->drivers;
    }

    /**
     * Check whether a driver wizard is registered
     *
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name): bool
    {
        return isset($this->drivers[$name]);
    }
}
