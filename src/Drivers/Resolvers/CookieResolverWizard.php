<?php
declare(strict_types=1);

namespace Sprout\Propagator\Drivers\Resolvers;

use Sprout\Propagator\Contracts\DriverWizard;
use Sprout\Propagator\Fields\GroupField;
use Sprout\Propagator\Fields\TextField;

/**
 * Cookie Resolver Wizard
 *
 * Driver wizard for the cookie identity resolver. Collects the cookie name
 * and optional cookie options (path and domain) for setting the identity
 * cookie on the response.
 *
 * @package Drivers\Resolvers
 */
final class CookieResolverWizard implements DriverWizard
{
    /**
     * Get the driver name
     *
     * @return string
     */
    public function getName(): string
    {
        return 'cookie';
    }

    /**
     * Get the human-readable label for the driver
     *
     * @return string
     */
    public function getLabel(): string
    {
        return 'Cookie';
    }

    /**
     * Get the field schema for this driver
     *
     * @return array<int, \Sprout\Propagator\Contracts\Field>
     */
    public function getFields(): array
    {
        return [
            TextField::make('cookie')
                ->default('{Tenancy}-Identifier'),
            GroupField::make('options')
                ->fields([
                    TextField::make('path')
                        ->default('/'),
                    TextField::make('domain'),
                ]),
        ];
    }
}
