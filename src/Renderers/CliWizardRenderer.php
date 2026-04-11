<?php
declare(strict_types=1);

namespace Sprout\Propagator\Renderers;

use Sprout\Propagator\Contracts\Field;
use Sprout\Propagator\Contracts\WizardRenderer;
use Sprout\Propagator\Fields\EnvField;
use Sprout\Propagator\Fields\GroupField;
use Sprout\Propagator\Fields\SelectField;
use Sprout\Propagator\Values\EnvValue;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

/**
 * CLI Wizard Renderer
 *
 * Renders wizard fields as interactive CLI prompts using laravel/prompts.
 *
 * @package Renderers
 */
final class CliWizardRenderer implements WizardRenderer
{
    /**
     * Render a field and collect input from the user
     *
     * @param \Sprout\Propagator\Contracts\Field $field
     * @param mixed                              $currentValue
     *
     * @return mixed
     */
    public function renderField(Field $field, mixed $currentValue = null): mixed
    {
        return match ($field->getType()) {
            'text', 'integer', 'class' => $this->renderTextField($field, $currentValue),
            'boolean'                  => $this->renderBooleanField($field, $currentValue),
            'select'                   => $this->renderSelectField($field, $currentValue),
            'env'                      => $this->renderEnvField($field, $currentValue),
            'group'                    => $this->renderGroupField($field, $currentValue),
            'array'                    => $this->renderArrayField($field, $currentValue),
            default                    => $this->renderTextField($field, $currentValue),
        };
    }

    /**
     * Present a list of options and collect a selection
     *
     * @param string                $label
     * @param array<string, string> $options
     *
     * @return string
     */
    public function selectFromList(string $label, array $options): string
    {
        return (string) select(
            label: $label,
            options: $options,
        );
    }

    /**
     * Ask the user for confirmation
     *
     * @param string $message
     *
     * @return bool
     */
    public function confirm(string $message): bool
    {
        return confirm(label: $message);
    }

    /**
     * Render a text-based field
     *
     * @param \Sprout\Propagator\Contracts\Field $field
     * @param mixed                              $currentValue
     *
     * @return string
     */
    private function renderTextField(Field $field, mixed $currentValue): string
    {
        $raw = $currentValue ?? $field->getDefault() ?? '';
        $default = is_scalar($raw) ? (string) $raw : '';

        return text(
            label: $field->getLabel(),
            default: $default,
            required: $field->isRequired(),
        );
    }

    /**
     * Render a boolean field
     *
     * @param \Sprout\Propagator\Contracts\Field $field
     * @param mixed                              $currentValue
     *
     * @return bool
     */
    private function renderBooleanField(Field $field, mixed $currentValue): bool
    {
        return confirm(
            label: $field->getLabel(),
            default: (bool) ($currentValue ?? $field->getDefault() ?? false),
        );
    }

    /**
     * Render a select field
     *
     * @param \Sprout\Propagator\Contracts\Field $field
     * @param mixed                              $currentValue
     *
     * @return string
     */
    private function renderSelectField(Field $field, mixed $currentValue): string
    {
        /** @var \Sprout\Propagator\Fields\SelectField $field */
        $default = $currentValue ?? $field->getDefault();

        return (string) select(
            label: $field->getLabel(),
            options: $field->getOptions(),
            default: is_string($default) || is_int($default) ? $default : null,
        );
    }

    /**
     * Render an env field
     *
     * Collects the env key and optionally a fallback value, returning
     * an EnvValue instance.
     *
     * @param \Sprout\Propagator\Contracts\Field $field
     * @param mixed                              $currentValue
     *
     * @return \Sprout\Propagator\Values\EnvValue
     */
    private function renderEnvField(Field $field, mixed $currentValue): EnvValue
    {
        /** @var \Sprout\Propagator\Fields\EnvField $field */
        $envKey = text(
            label: $field->getLabel() . ' (env variable name)',
            default: $field->getEnvKey() ?? '',
            required: $field->isRequired(),
        );

        $fallback = null;
        $fallbackField = $field->getFallback();

        if ($fallbackField !== null) {
            $currentFallback = $currentValue instanceof EnvValue ? $currentValue->fallback : null;
            $fallback = $this->renderField($fallbackField, $currentFallback);
        }

        return new EnvValue($envKey, $fallback);
    }

    /**
     * Render a group field by rendering each nested field
     *
     * @param \Sprout\Propagator\Contracts\Field $field
     * @param mixed                              $currentValue
     *
     * @return array<string, mixed>
     */
    private function renderGroupField(Field $field, mixed $currentValue): array
    {
        /** @var \Sprout\Propagator\Fields\GroupField $field */
        $values = [];
        $current = is_array($currentValue) ? $currentValue : [];

        foreach ($field->getFields() as $child) {
            $values[$child->getName()] = $this->renderField(
                $child,
                $current[$child->getName()] ?? null,
            );
        }

        return $values;
    }

    /**
     * Render an array field by collecting items until the user stops
     *
     * @param \Sprout\Propagator\Contracts\Field $field
     * @param mixed                              $currentValue
     *
     * @return array<int, string>
     */
    private function renderArrayField(Field $field, mixed $currentValue): array
    {
        $default = is_array($currentValue) ? $currentValue : ($field->getDefault() ?? []);

        /** @var array<int, string> $items */
        $items = is_array($default) ? $default : [];

        while (true) {
            $value = text(
                label: $field->getLabel() . ' (leave empty to finish)',
                default: '',
            );

            if ($value === '') {
                break;
            }

            $items[] = $value;
        }

        return $items;
    }
}
