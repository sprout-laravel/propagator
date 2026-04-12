<?php
declare(strict_types=1);

namespace Sprout\Propagator\Support;

use InvalidArgumentException;
use Sprout\Propagator\Contracts\CategoryRegistry;
use Sprout\Propagator\Contracts\ConfigCategory;

/**
 * Default Category Registry
 *
 * The default implementation of the category registry, storing categories
 * in an array keyed by name.
 */
final class DefaultCategoryRegistry implements CategoryRegistry
{
    /**
     * The registered categories
     *
     * @var array<string, ConfigCategory>
     */
    private array $categories = [];

    /**
     * Register a config category
     *
     * @param ConfigCategory $category
     *
     * @return void
     */
    public function register(ConfigCategory $category): void
    {
        $this->categories[$category->getName()] = $category;
    }

    /**
     * Get a category by name
     *
     * @param string $name
     *
     * @return ConfigCategory
     *
     * @throws InvalidArgumentException
     */
    public function get(string $name): ConfigCategory
    {
        if (! $this->has($name)) {
            throw new InvalidArgumentException("Config category [{$name}] is not registered.");
        }

        return $this->categories[$name];
    }

    /**
     * Get all registered categories
     *
     * @return array<string, ConfigCategory>
     */
    public function all(): array
    {
        return $this->categories;
    }

    /**
     * Check whether a category is registered
     *
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name): bool
    {
        return isset($this->categories[$name]);
    }
}
