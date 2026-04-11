<?php
declare(strict_types=1);

namespace Sprout\Propagator\Drivers\Providers;

use Sprout\Propagator\Contracts\DriverWizard;
use Sprout\Propagator\Fields\ClassField;
use Sprout\Propagator\Fields\TextField;

/**
 * Database Provider Wizard
 *
 * Driver wizard for the database tenant provider. Collects the database table,
 * optional entity class, and optional connection name.
 *
 * @package Drivers\Providers
 */
final class DatabaseProviderWizard implements DriverWizard
{
    /**
     * Get the driver name
     *
     * @return string
     */
    public function getName(): string
    {
        return 'database';
    }

    /**
     * Get the human-readable label for the driver
     *
     * @return string
     */
    public function getLabel(): string
    {
        return 'Database';
    }

    /**
     * Get the field schema for this driver
     *
     * @return array<int, \Sprout\Propagator\Contracts\Field>
     */
    public function getFields(): array
    {
        return [
            TextField::make('table')
                ->required(),
            ClassField::make('entity'),
            TextField::make('connection'),
        ];
    }
}
