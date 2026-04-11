<?php
declare(strict_types=1);

namespace Sprout\Propagator\Tests\Support;

use Sprout\Propagator\Contracts\Field;
use Sprout\Propagator\Contracts\WizardRenderer;

/**
 * Mock Wizard Renderer
 *
 * A test double that returns values from a queue to simulate user input.
 */
final class MockWizardRenderer implements WizardRenderer
{
    /**
     * @var array<int, mixed>
     */
    private array $responses;

    /**
     * @var int
     */
    private int $index = 0;

    /**
     * @param array<int, mixed> $responses
     */
    public function __construct(array $responses = [])
    {
        $this->responses = $responses;
    }

    public function renderField(Field $field, mixed $currentValue = null): mixed
    {
        return $this->next();
    }

    public function selectFromList(string $label, array $options): string
    {
        return (string) $this->next();
    }

    public function confirm(string $message): bool
    {
        return (bool) $this->next();
    }

    private function next(): mixed
    {
        if (! isset($this->responses[$this->index])) {
            throw new \RuntimeException(
                'MockWizardRenderer: no more responses available (index ' . $this->index . ')'
            );
        }

        return $this->responses[$this->index++];
    }
}
