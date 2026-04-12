<?php
declare(strict_types=1);

namespace Sprout\Propagator\Categories;

use Sprout\Propagator\Drivers\Tenancies\DefaultTenancyWizard;

/**
 * Tenancies Category
 *
 * Config category for tenancies. Targets the 'tenancies' key
 * within the multitenancy config file.
 */
final class TenanciesCategory extends BaseConfigCategory
{
    /**
     * Get the name of the category
     *
     * @return string
     */
    public function getName(): string
    {
        return 'tenancies';
    }

    /**
     * Get the human-readable label for the category
     *
     * @return string
     */
    public function getLabel(): string
    {
        return 'Tenancies';
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
        return 'tenancies';
    }

    /**
     * Register the built-in tenancy drivers
     *
     * @return void
     */
    protected function registerDrivers(): void
    {
        $this->drivers()->add(new DefaultTenancyWizard());
    }
}
