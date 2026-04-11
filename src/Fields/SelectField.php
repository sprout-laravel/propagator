<?php
declare(strict_types=1);

namespace Sprout\Propagator\Fields;

/**
 * Select Field
 *
 * A field that presents a list of options for the user to choose from.
 *
 * @package Fields
 */
final class SelectField extends BaseField
{
    /**
     * The available options
     *
     * @var array<string, string>
     */
    private array $options = [];

    /**
     * Create a new select field
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
     * Get the type identifier for this field
     *
     * @return string
     */
    public function getType(): string
    {
        return 'select';
    }

    /**
     * Set the available options
     *
     * @param array<string, string> $options
     *
     * @return self
     */
    public function options(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Get the available options
     *
     * @return array<string, string>
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
