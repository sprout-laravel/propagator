<?php
declare(strict_types=1);

namespace Sprout\Propagator\Categories;

use Sprout\Propagator\Values\ClassReference;

/**
 * Overrides Category
 *
 * Config category for service overrides. Targets the root of the overrides
 * config file. Unlike other categories, driver values are wrapped in a
 * ClassReference because override drivers are identified by their FQCN
 * rather than a short string alias.
 *
 * @package Categories
 */
final class OverridesCategory extends BaseConfigCategory
{
    /**
     * Get the name of the category
     *
     * @return string
     */
    public function getName(): string
    {
        return 'overrides';
    }

    /**
     * Get the human-readable label for the category
     *
     * @return string
     */
    public function getLabel(): string
    {
        return 'Service Overrides';
    }

    /**
     * Get the config file name this category targets
     *
     * @return string
     */
    public function getConfigFile(): string
    {
        return 'overrides';
    }

    /**
     * Get the dot-notation key within the config file
     *
     * Returns an empty string because this category targets the root of the
     * overrides config file rather than a nested key.
     *
     * @return string
     */
    public function getConfigKey(): string
    {
        return '';
    }

    /**
     * Build a config entry with the driver wrapped in a ClassReference
     *
     * Service override drivers are identified by their FQCN, so the driver
     * value is wrapped in a ClassReference for correct serialisation.
     *
     * @param string               $name
     * @param string               $driver
     * @param array<string, mixed> $fieldValues
     *
     * @phpstan-param class-string $driver
     *
     * @return array<string, mixed>
     */
    public function buildEntry(string $name, string $driver, array $fieldValues): array
    {
        return array_merge(['driver' => new ClassReference($driver)], $fieldValues);
    }

    /**
     * Register the built-in override drivers
     *
     * @return void
     */
    protected function registerDrivers(): void
    {
        // I'm intentionally empty
    }
}
