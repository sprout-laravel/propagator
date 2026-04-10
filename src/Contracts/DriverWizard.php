<?php

namespace Sprout\Propagator\Contracts;

/**
 * Driver Wizard Contract
 *
 * This contract marks a class as being a wizard definition for a specific
 * driver within a config category. It declares the field schema needed
 * to configure the driver.
 *
 * @package Contracts
 */
interface DriverWizard
{
    /**
     * Get the driver name
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get the human-readable label for the driver
     *
     * @return string
     */
    public function getLabel(): string;

    /**
     * Get the field schema for this driver
     *
     * @return array<int, \Sprout\Propagator\Contracts\Field>
     */
    public function getFields(): array;
}
