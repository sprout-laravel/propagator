<?php

namespace Sprout\Propagator\Contracts;

use Sprout\Propagator\Fields\FieldDependency;

/**
 * Field Contract
 *
 * This contract marks a class as being a field definition within a wizard
 * driver's field schema. Fields describe what data to collect, not how
 * to render it.
 */
interface Field
{
    /**
     * Get the name of the field
     *
     * This is the key used in the config array output.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get the human-readable label for the field
     *
     * @return string
     */
    public function getLabel(): string;

    /**
     * Get the type identifier for this field
     *
     * Used by renderers to determine how to collect input.
     *
     * @return string
     */
    public function getType(): string;

    /**
     * Get the default value for this field
     *
     * @return mixed
     */
    public function getDefault(): mixed;

    /**
     * Check whether this field is required
     *
     * @return bool
     */
    public function isRequired(): bool;

    /**
     * Get the validation rules for this field
     *
     * @return array<int, string>
     */
    public function getRules(): array;

    /**
     * Get the cross-field dependency for this field
     *
     * If set, the field should only be presented when the dependency
     * condition is met.
     *
     * @return FieldDependency|null
     */
    public function getDependency(): ?FieldDependency;
}
