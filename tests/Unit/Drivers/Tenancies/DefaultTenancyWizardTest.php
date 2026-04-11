<?php
declare(strict_types=1);

namespace Sprout\Propagator\Tests\Unit\Drivers\Tenancies;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sprout\Propagator\Drivers\Tenancies\DefaultTenancyWizard;
use Sprout\Propagator\Fields\ArrayField;
use Sprout\Propagator\Fields\TextField;

#[CoversClass(DefaultTenancyWizard::class)]
final class DefaultTenancyWizardTest extends TestCase
{
    #[Test]
    public function it_has_the_correct_name_and_label(): void
    {
        $wizard = new DefaultTenancyWizard();

        $this->assertSame('default', $wizard->getName());
        $this->assertSame('Default', $wizard->getLabel());
    }

    #[Test]
    public function it_defines_provider_and_options_fields(): void
    {
        $wizard = new DefaultTenancyWizard();
        $fields = $wizard->getFields();

        $this->assertCount(2, $fields);

        $provider = $fields[0];
        $this->assertInstanceOf(TextField::class, $provider);
        $this->assertSame('provider', $provider->getName());
        $this->assertTrue($provider->isRequired());

        $options = $fields[1];
        $this->assertInstanceOf(ArrayField::class, $options);
        $this->assertSame('options', $options->getName());
        $this->assertSame([], $options->getDefault());
    }
}
