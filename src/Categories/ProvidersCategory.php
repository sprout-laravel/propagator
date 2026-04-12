<?php
declare(strict_types=1);

namespace Sprout\Propagator\Categories;

use Sprout\Propagator\Drivers\Providers\DatabaseProviderWizard;
use Sprout\Propagator\Drivers\Providers\EloquentProviderWizard;

/**
 * Providers Category
 *
 * Config category for tenant providers. Targets the 'providers' key
 * within the multitenancy config file.
 */
final class ProvidersCategory extends BaseConfigCategory
{
    /**
     * Get the name of the category
     *
     * @return string
     */
    public function getName(): string
    {
        return 'providers';
    }

    /**
     * Get the human-readable label for the category
     *
     * @return string
     */
    public function getLabel(): string
    {
        return 'Tenant Providers';
    }

    /**
     * Get the config file name this category targets
     *
     * @return string
     */
    public function getConfigFile(): string
    {
        return 'multitenancy';
    }

    /**
     * Get the dot-notation key within the config file
     *
     * @return string
     */
    public function getConfigKey(): string
    {
        return 'providers';
    }

    /**
     * Register the built-in provider drivers
     *
     * @return void
     */
    protected function registerDrivers(): void
    {
        $this->drivers()->add(new EloquentProviderWizard());
        $this->drivers()->add(new DatabaseProviderWizard());
    }
}
