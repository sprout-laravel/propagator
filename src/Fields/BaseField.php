<?php
declare(strict_types=1);

namespace Sprout\Propagator\Fields;

use Sprout\Propagator\Contracts\Field;

/**
 * Base Field
 *
 * Abstract base class for all field types. Provides the fluent builder API
 * and common state. Each concrete field type extends this class and
 * provides its own type identifier.
 */
abstract class BaseField implements Field
{
    /**
     * The human-readable label
     *
     * @var string
     */
    private string $label;

    /**
     * The default value
     *
     * @var mixed
     */
    private mixed $default = null;

    /**
     * Whether the field is required
     *
     * @var bool
     */
    private bool $required = false;

    /**
     * Validation rules
     *
     * @var array<int, string>
     */
    private array $rules = [];

    /**
     * Cross-field dependency
     *
     * @var FieldDependency|null
     */
    private ?FieldDependency $dependency = null;

    /**
     * Create a new instance
     *
     * @param string $name
     */
    public function __construct(
        private readonly string $name,
    ) {
        $this->label = $name;
    }

    /**
     * Get the name of the field
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the human-readable label for the field
     *
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Get the default value for this field
     *
     * @return mixed
     */
    public function getDefault(): mixed
    {
        return $this->default;
    }

    /**
     * Check whether this field is required
     *
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * Get the validation rules for this field
     *
     * @return array<int, string>
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * Get the cross-field dependency for this field
     *
     * @return FieldDependency|null
     */
    public function getDependency(): ?FieldDependency
    {
        return $this->dependency;
    }

    /**
     * Set the human-readable label
     *
     * @param string $label
     *
     * @return static
     */
    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Set the default value
     *
     * @param mixed $default
     *
     * @return static
     */
    public function default(mixed $default): static
    {
        $this->default = $default;

        return $this;
    }

    /**
     * Mark the field as required
     *
     * @return static
     */
    public function required(): static
    {
        $this->required = true;

        return $this;
    }

    /**
     * Set validation rules
     *
     * @param array<int, string> $rules
     *
     * @return static
     */
    public function rules(array $rules): static
    {
        $this->rules = $rules;

        return $this;
    }

    /**
     * Set a cross-field dependency
     *
     * @param FieldDependency $dependency
     *
     * @return static
     */
    public function dependsOn(FieldDependency $dependency): static
    {
        $this->dependency = $dependency;

        return $this;
    }
}
