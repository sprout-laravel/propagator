<?php
declare(strict_types=1);

namespace Sprout\Propagator\Drivers\Resolvers;

use Sprout\Propagator\Contracts\DriverWizard;
use Sprout\Propagator\Contracts\Field;
use Sprout\Propagator\Fields\TextField;

/**
 * Session Resolver Wizard
 *
 * Driver wizard for the session identity resolver. Collects the session key
 * under which the tenant identifier is stored.
 */
final class SessionResolverWizard implements DriverWizard
{
    /**
     * Get the driver name
     *
     * @return string
     */
    public function getName(): string
    {
        return 'session';
    }

    /**
     * Get the human-readable label for the driver
     *
     * @return string
     */
    public function getLabel(): string
    {
        return 'Session';
    }

    /**
     * Get the field schema for this driver
     *
     * @return array<int, Field>
     */
    public function getFields(): array
    {
        return [
            TextField::make('session')
                ->default('multitenancy.{tenancy}'),
        ];
    }
}
