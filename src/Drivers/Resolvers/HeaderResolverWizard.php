<?php
declare(strict_types=1);

namespace Sprout\Propagator\Drivers\Resolvers;

use Sprout\Propagator\Contracts\DriverWizard;
use Sprout\Propagator\Fields\TextField;

/**
 * Header Resolver Wizard
 *
 * Driver wizard for the header identity resolver. Collects the HTTP header
 * name from which the tenant identifier is extracted.
 *
 * @package Drivers\Resolvers
 */
final class HeaderResolverWizard implements DriverWizard
{
    /**
     * Get the driver name
     *
     * @return string
     */
    public function getName(): string
    {
        return 'header';
    }

    /**
     * Get the human-readable label for the driver
     *
     * @return string
     */
    public function getLabel(): string
    {
        return 'Header';
    }

    /**
     * Get the field schema for this driver
     *
     * @return array<int, \Sprout\Propagator\Contracts\Field>
     */
    public function getFields(): array
    {
        return [
            TextField::make('header')
                ->default('{Tenancy}-Identifier'),
        ];
    }
}
