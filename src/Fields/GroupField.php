<?php
declare(strict_types=1);

namespace Sprout\Propagator\Fields;

use Sprout\Propagator\Contracts\Field;

/**
 * Group Field
 *
 * A field that contains nested sub-fields, producing a nested array
 * in the config output.
 */
final class GroupField extends BaseField
{
    /**
     * Create a new group field
     *
     * @param string $name
     *
     * @return self
     */
    public static function make(string $name): self
    {
        return new self($name);
    }

    /**
     * The nested fields
     *
     * @var array<int, Field>
     */
    private array $fields = [];

    /**
     * Get the type identifier for this field
     *
     * @return string
     */
    public function getType(): string
    {
        return 'group';
    }

    /**
     * Set the nested fields
     *
     * @param array<int, Field> $fields
     *
     * @return self
     */
    public function fields(array $fields): self
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * Get the nested fields
     *
     * @return array<int, Field>
     */
    public function getFields(): array
    {
        return $this->fields;
    }
}
