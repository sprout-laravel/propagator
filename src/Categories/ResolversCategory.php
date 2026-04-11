<?php
declare(strict_types=1);

namespace Sprout\Propagator\Categories;

use Sprout\Propagator\Drivers\Resolvers\CookieResolverWizard;
use Sprout\Propagator\Drivers\Resolvers\HeaderResolverWizard;
use Sprout\Propagator\Drivers\Resolvers\PathResolverWizard;
use Sprout\Propagator\Drivers\Resolvers\SessionResolverWizard;
use Sprout\Propagator\Drivers\Resolvers\SubdomainResolverWizard;

/**
 * Resolvers Category
 *
 * Config category for identity resolvers. Targets the 'resolvers' key
 * within the multitenancy config file.
 *
 * @package Categories
 */
final class ResolversCategory extends BaseConfigCategory
{
    /**
     * Get the name of the category
     *
     * @return string
     */
    public function getName(): string
    {
        return 'resolvers';
    }

    /**
     * Get the human-readable label for the category
     *
     * @return string
     */
    public function getLabel(): string
    {
        return 'Identity Resolvers';
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
        return 'resolvers';
    }

    /**
     * Register the built-in resolver drivers
     *
     * @return void
     */
    protected function registerDrivers(): void
    {
        $this->drivers()->add(new SubdomainResolverWizard());
        $this->drivers()->add(new PathResolverWizard());
        $this->drivers()->add(new HeaderResolverWizard());
        $this->drivers()->add(new CookieResolverWizard());
        $this->drivers()->add(new SessionResolverWizard());
    }
}
