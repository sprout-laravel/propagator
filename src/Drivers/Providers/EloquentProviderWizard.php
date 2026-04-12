<?php
declare(strict_types=1);

namespace Sprout\Propagator\Drivers\Providers;

use Sprout\Propagator\Contracts\DriverWizard;
use Sprout\Propagator\Contracts\Field;
use Sprout\Propagator\Fields\ClassField;

/**
 * Eloquent Provider Wizard
 *
 * Driver wizard for the Eloquent tenant provider. Collects the Eloquent model
 * class that represents the tenant.
 */
final class EloquentProviderWizard implements DriverWizard
{
    /**
     * Get the driver name
     *
     * @return string
     */
    public function getName(): string
    {
        return 'eloquent';
    }

    /**
     * Get the human-readable label for the driver
     *
     * @return string
     */
    public function getLabel(): string
    {
        return 'Eloquent';
    }

    /**
     * Get the field schema for this driver
     *
     * @return array<int, Field>
     */
    public function getFields(): array
    {
        return [
            ClassField::make('model')
                ->label('Tenant Model')
                ->required(),
        ];
    }
}
