<?php
declare(strict_types=1);

namespace Sprout\Propagator\Drivers\Resolvers;

use Sprout\Propagator\Contracts\DriverWizard;
use Sprout\Propagator\Contracts\Field;
use Sprout\Propagator\Fields\TextField;

/**
 * Header Resolver Wizard
 *
 * Driver wizard for the header identity resolver. Collects the HTTP header
 * name from which the tenant identifier is extracted.
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
     * @return array<int, Field>
     */
    public function getFields(): array
    {
        return [
            TextField::make('header')
                ->default('{Tenancy}-Identifier'),
        ];
    }
}
