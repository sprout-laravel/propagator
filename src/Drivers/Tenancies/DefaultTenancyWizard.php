<?php
declare(strict_types=1);

namespace Sprout\Propagator\Drivers\Tenancies;

use Sprout\Propagator\Contracts\DriverWizard;
use Sprout\Propagator\Contracts\Field;
use Sprout\Propagator\Fields\ArrayField;
use Sprout\Propagator\Fields\TextField;

/**
 * Default Tenancy Wizard
 *
 * Driver wizard for the default tenancy driver. Collects the provider name
 * that the tenancy will use to load tenants, and an optional options array
 * for additional tenancy configuration.
 */
final class DefaultTenancyWizard implements DriverWizard
{
    /**
     * Get the driver name
     *
     * @return string
     */
    public function getName(): string
    {
        return 'default';
    }

    /**
     * Get the human-readable label for the driver
     *
     * @return string
     */
    public function getLabel(): string
    {
        return 'Default';
    }

    /**
     * Get the field schema for this driver
     *
     * @return array<int, Field>
     */
    public function getFields(): array
    {
        return [
            TextField::make('provider')
                ->required(),
            ArrayField::make('options')
                ->default([]),
        ];
    }
}
