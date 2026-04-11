<?php
declare(strict_types=1);

namespace Sprout\Propagator\Tests\Unit\Drivers\Providers;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sprout\Propagator\Drivers\Providers\EloquentProviderWizard;
use Sprout\Propagator\Fields\ClassField;

#[CoversClass(EloquentProviderWizard::class)]
final class EloquentProviderWizardTest extends TestCase
{
    #[Test]
    public function it_has_the_correct_name_and_label(): void
    {
        $wizard = new EloquentProviderWizard();

        $this->assertSame('eloquent', $wizard->getName());
        $this->assertSame('Eloquent', $wizard->getLabel());
    }

    #[Test]
    public function it_defines_a_required_model_field(): void
    {
        $wizard = new EloquentProviderWizard();
        $fields = $wizard->getFields();

        $this->assertCount(1, $fields);

        $model = $fields[0];
        $this->assertInstanceOf(ClassField::class, $model);
        $this->assertSame('model', $model->getName());
        $this->assertSame('Tenant Model', $model->getLabel());
        $this->assertTrue($model->isRequired());
    }
}
