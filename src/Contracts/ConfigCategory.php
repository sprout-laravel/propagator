<?php

namespace Sprout\Propagator\Contracts;

use Illuminate\Contracts\Config\Repository;

/**
 * Config Category Contract
 *
 * This contract marks a class as being a configurable category within Sprout.
 * Each category knows which config file and key it targets, how to read
 * existing entries, and how to build new entries from wizard-collected values.
 */
interface ConfigCategory
{
    /**
     * Get the name of the category
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get the human-readable label for the category
     *
     * @return string
     */
    public function getLabel(): string;

    /**
     * Get the config file name this category targets
     *
     * This is the config file name without the .php extension,
     * e.g. 'multitenancy' for config/multitenancy.php.
     *
     * @return string
     */
    public function getConfigFile(): string;

    /**
     * Get the dot-notation key within the config file
     *
     * This is the path to the array within the config file that contains
     * the entries for this category, e.g. 'resolvers' or 'overrides.services'.
     *
     * @return string
     */
    public function getConfigKey(): string;

    /**
     * Get all existing entries for this category
     *
     * @param Repository $config
     *
     * @return array<string, array<string, mixed>>
     */
    public function getEntries(Repository $config): array;

    /**
     * Get a single existing entry by name
     *
     * @param string     $name
     * @param Repository $config
     *
     * @return array<string, mixed>|null
     */
    public function getEntry(string $name, Repository $config): ?array;

    /**
     * Build a config entry from wizard-collected values
     *
     * The returned array may contain value objects (EnvValue, ClassReference,
     * MethodCall) alongside scalars. The config writer handles serialisation.
     *
     * @param string               $name
     * @param string               $driver
     * @param array<string, mixed> $fieldValues
     *
     * @return array<string, mixed>
     */
    public function buildEntry(string $name, string $driver, array $fieldValues): array;

    /**
     * Get the driver registry for this category
     *
     * @return DriverRegistry
     */
    public function drivers(): DriverRegistry;
}
