<?php

namespace Sprout\Propagator\Contracts;

/**
 * Wizard Renderer Contract
 *
 * This contract abstracts user interaction for the wizard system.
 * The command orchestrates flow and evaluates dependencies; the renderer
 * handles input collection.
 *
 * @package Contracts
 */
interface WizardRenderer
{
    /**
     * Render a field and collect input from the user
     *
     * @param \Sprout\Propagator\Contracts\Field $field
     * @param mixed                              $currentValue
     *
     * @return mixed
     */
    public function renderField(Field $field, mixed $currentValue = null): mixed;

    /**
     * Present a list of options and collect a selection
     *
     * @param string                $label
     * @param array<string, string> $options
     *
     * @return string
     */
    public function selectFromList(string $label, array $options): string;

    /**
     * Ask the user for confirmation
     *
     * @param string $message
     *
     * @return bool
     */
    public function confirm(string $message): bool;
}
