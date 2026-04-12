<?php
declare(strict_types=1);

namespace Sprout\Propagator\Drivers\Resolvers;

use Sprout\Propagator\Contracts\DriverWizard;
use Sprout\Propagator\Contracts\Field;
use Sprout\Propagator\Fields\EnvField;
use Sprout\Propagator\Fields\TextField;

/**
 * Subdomain Resolver Wizard
 *
 * Driver wizard for the subdomain identity resolver. Collects the domain
 * (as an env reference) and an optional pattern to match tenant identifiers
 * within subdomains.
 */
final class SubdomainResolverWizard implements DriverWizard
{
    /**
     * Get the driver name
     *
     * @return string
     */
    public function getName(): string
    {
        return 'subdomain';
    }

    /**
     * Get the human-readable label for the driver
     *
     * @return string
     */
    public function getLabel(): string
    {
        return 'Subdomain';
    }

    /**
     * Get the field schema for this driver
     *
     * @return array<int, Field>
     */
    public function getFields(): array
    {
        return [
            EnvField::make('domain')
                ->envKey('TENANTED_DOMAIN')
                ->required(),
            TextField::make('pattern')
                ->default('.*'),
        ];
    }
}
