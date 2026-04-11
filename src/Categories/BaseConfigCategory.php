<?php
declare(strict_types=1);

namespace Sprout\Propagator\Categories;

use Illuminate\Contracts\Config\Repository;
use Sprout\Propagator\Contracts\ConfigCategory;
use Sprout\Propagator\Contracts\DriverRegistry;
use Sprout\Propagator\Support\DefaultDriverRegistry;

/**
 * Base Config Category
 *
 * Abstract base class for config categories. Provides common config reading
 * logic and driver registry management. Subclasses define the config file,
 * key, and entry structure.
 *
 * @package Categories
 */
abstract class BaseConfigCategory implements ConfigCategory
{
    /**
     * The driver registry for this category
     *
     * @var \Sprout\Propagator\Contracts\DriverRegistry
     */
    private DriverRegistry $drivers;

    /**
     * Create a new instance
     */
    public function __construct()
    {
        $this->drivers = new DefaultDriverRegistry();
        $this->registerDrivers();
    }

    /**
     * Register the built-in drivers for this category
     *
     * @return void
     */
    abstract protected function registerDrivers(): void;

    /**
     * Get the full config path for reading from the repository
     *
     * @return string
     */
    private function getFullConfigPath(): string
    {
        $key = $this->getConfigKey();

        if ($key === '') {
            return $this->getConfigFile();
        }

        return $this->getConfigFile() . '.' . $key;
    }

    /**
     * Get all existing entries for this category
     *
     * @param \Illuminate\Contracts\Config\Repository $config
     *
     * @return array<string, array<string, mixed>>
     */
    public function getEntries(Repository $config): array
    {
        /** @var array<string, array<string, mixed>> $entries */
        $entries = $config->get($this->getFullConfigPath(), []);

        return $entries;
    }

    /**
     * Get a single existing entry by name
     *
     * @param string                                  $name
     * @param \Illuminate\Contracts\Config\Repository $config
     *
     * @return array<string, mixed>|null
     */
    public function getEntry(string $name, Repository $config): ?array
    {
        /** @var array<string, mixed>|null $entry */
        $entry = $config->get($this->getFullConfigPath() . '.' . $name);

        return $entry;
    }

    /**
     * Build a config entry from wizard-collected values
     *
     * Default implementation prepends the driver key. Subclasses may
     * override to handle different entry structures.
     *
     * @param string               $name
     * @param string               $driver
     * @param array<string, mixed> $fieldValues
     *
     * @return array<string, mixed>
     */
    public function buildEntry(string $name, string $driver, array $fieldValues): array
    {
        return array_merge(['driver' => $driver], $fieldValues);
    }

    /**
     * Get the driver registry for this category
     *
     * @return \Sprout\Propagator\Contracts\DriverRegistry
     */
    public function drivers(): DriverRegistry
    {
        return $this->drivers;
    }
}
