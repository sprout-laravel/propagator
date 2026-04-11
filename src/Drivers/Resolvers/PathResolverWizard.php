<?php
declare(strict_types=1);

namespace Sprout\Propagator\Drivers\Resolvers;

use Sprout\Propagator\Contracts\DriverWizard;
use Sprout\Propagator\Fields\IntegerField;

/**
 * Path Resolver Wizard
 *
 * Driver wizard for the path identity resolver. Collects the URL path segment
 * position from which the tenant identifier is extracted.
 *
 * @package Drivers\Resolvers
 */
final class PathResolverWizard implements DriverWizard
{
    /**
     * Get the driver name
     *
     * @return string
     */
    public function getName(): string
    {
        return 'path';
    }

    /**
     * Get the human-readable label for the driver
     *
     * @return string
     */
    public function getLabel(): string
    {
        return 'Path';
    }

    /**
     * Get the field schema for this driver
     *
     * @return array<int, \Sprout\Propagator\Contracts\Field>
     */
    public function getFields(): array
    {
        return [
            IntegerField::make('segment')
                ->default(1),
        ];
    }
}
