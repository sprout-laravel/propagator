<?php
declare(strict_types=1);

namespace Sprout\Propagator\Fields;

use Sprout\Propagator\Contracts\Field;

/**
 * Env Field
 *
 * A field that represents an environment variable reference in config.
 * Supports recursive fallbacks where the fallback can be any other field,
 * including another EnvField.
 */
final class EnvField extends BaseField
{
    /**
     * Create a new env field
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
     * The environment variable key
     *
     * @var string|null
     */
    private ?string $envKey = null;

    /**
     * The fallback field
     *
     * @var Field|null
     */
    private ?Field $fallback = null;

    /**
     * Get the type identifier for this field
     *
     * @return string
     */
    public function getType(): string
    {
        return 'env';
    }

    /**
     * Set the environment variable key
     *
     * @param string $envKey
     *
     * @return self
     */
    public function envKey(string $envKey): self
    {
        $this->envKey = $envKey;

        return $this;
    }

    /**
     * Get the environment variable key
     *
     * @return string|null
     */
    public function getEnvKey(): ?string
    {
        return $this->envKey;
    }

    /**
     * Set the fallback field
     *
     * The fallback can be any field type, including another EnvField
     * for recursive env() calls.
     *
     * @param Field $fallback
     *
     * @return self
     */
    public function fallback(Field $fallback): self
    {
        $this->fallback = $fallback;

        return $this;
    }

    /**
     * Get the fallback field
     *
     * @return Field|null
     */
    public function getFallback(): ?Field
    {
        return $this->fallback;
    }
}
