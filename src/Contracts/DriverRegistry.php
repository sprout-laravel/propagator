<?php

namespace Sprout\Propagator\Contracts;

/**
 * Driver Registry Contract
 *
 * This contract defines a registry of driver wizards within a single
 * config category.
 *
 * @package Contracts
 */
interface DriverRegistry
{
    /**
     * Add a driver wizard to the registry
     *
     * @param \Sprout\Propagator\Contracts\DriverWizard $driver
     *
     * @return void
     */
    public function add(DriverWizard $driver): void;

    /**
     * Get a driver wizard by name
     *
     * @param string $name
     *
     * @return \Sprout\Propagator\Contracts\DriverWizard
     */
    public function get(string $name): DriverWizard;

    /**
     * Get all registered driver wizards
     *
     * @return array<string, \Sprout\Propagator\Contracts\DriverWizard>
     */
    public function all(): array;

    /**
     * Check whether a driver wizard is registered
     *
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name): bool;
}
