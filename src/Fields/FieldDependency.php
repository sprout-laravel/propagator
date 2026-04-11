<?php
declare(strict_types=1);

namespace Sprout\Propagator\Fields;

/**
 * Field Dependency
 *
 * Represents a cross-field dependency condition. A field with a dependency
 * should only be presented when the condition is met.
 *
 * @package Fields
 */
final class FieldDependency
{
    /**
     * Create a new instance
     *
     * @param string $fieldName
     * @param mixed  $expectedValue
     */
    private function __construct(
        private readonly string $fieldName,
        private readonly mixed  $expectedValue,
    )
    {
    }

    /**
     * Create a dependency that is met when a field has a specific value
     *
     * @param string $fieldName
     * @param mixed  $expectedValue
     *
     * @return self
     */
    public static function when(string $fieldName, mixed $expectedValue): self
    {
        return new self($fieldName, $expectedValue);
    }

    /**
     * Get the name of the field this depends on
     *
     * @return string
     */
    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    /**
     * Get the expected value for the dependency to be met
     *
     * @return mixed
     */
    public function getExpectedValue(): mixed
    {
        return $this->expectedValue;
    }

    /**
     * Check whether the dependency is met given a set of collected values
     *
     * @param array<string, mixed> $collectedValues
     *
     * @return bool
     */
    public function isMet(array $collectedValues): bool
    {
        if (! array_key_exists($this->fieldName, $collectedValues)) {
            return false;
        }

        return $collectedValues[$this->fieldName] === $this->expectedValue;
    }
}
