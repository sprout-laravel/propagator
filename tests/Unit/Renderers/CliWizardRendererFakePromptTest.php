<?php
declare(strict_types=1);

namespace Sprout\Propagator\Tests\Unit\Renderers;

use Laravel\Prompts\Key;
use Laravel\Prompts\Prompt;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\Attributes\Test;
use Sprout\Propagator\Fields\ArrayField;
use Sprout\Propagator\PropagatorServiceProvider;
use Sprout\Propagator\Renderers\CliWizardRenderer;
use Sprout\Propagator\Tests\Unit\UnitTestCase;

/**
 * Tests for CliWizardRenderer that require Prompt::fake() with key simulation.
 *
 * Separated from the main CliWizardRendererTest because these tests use
 * Prompt::fake() (interactive mode with mocked terminal) rather than
 * Prompt::interactive(false) (non-interactive fallback mode). Mixing the
 * two approaches in a single test class causes PCOV coverage tracking issues.
 */
#[CoversClass(CliWizardRenderer::class)]
final class CliWizardRendererFakePromptTest extends UnitTestCase
{
    protected function getPackageProviders($app): array
    {
        return [PropagatorServiceProvider::class];
    }

    #[Test]
    #[RunInSeparateProcess]
    public function select_from_list_returns_selected_option(): void
    {
        Prompt::fake([Key::ENTER]);

        $renderer = new CliWizardRenderer();
        $result = $renderer->selectFromList('Pick one', [
            'first'  => 'First Option',
            'second' => 'Second Option',
        ]);

        $this->assertSame('first', $result);
    }

    #[Test]
    #[RunInSeparateProcess]
    public function render_array_field_adds_items_until_empty_input(): void
    {
        Prompt::fake([
            'i', 't', 'e', 'm', '1', Key::ENTER,
            Key::ENTER,
        ]);

        $renderer = new CliWizardRenderer();
        $field = ArrayField::make('items')->label('Items');

        $result = $renderer->renderField($field);

        $this->assertIsArray($result);
        $this->assertContains('item1', $result);
    }
}
