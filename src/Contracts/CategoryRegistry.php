<?php

namespace Sprout\Propagator\Contracts;

/**
 * Category Registry Contract
 *
 * This contract defines the top-level registry that holds all configurable
 * categories. Packages register categories during boot.
 */
interface CategoryRegistry
{
    /**
     * Register a config category
     *
     * @param ConfigCategory $category
     *
     * @return void
     */
    public function register(ConfigCategory $category): void;

    /**
     * Get a category by name
     *
     * @param string $name
     *
     * @return ConfigCategory
     */
    public function get(string $name): ConfigCategory;

    /**
     * Get all registered categories
     *
     * @return array<string, ConfigCategory>
     */
    public function all(): array;

    /**
     * Check whether a category is registered
     *
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name): bool;
}
