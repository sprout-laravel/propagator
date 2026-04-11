<?php
declare(strict_types=1);

namespace Sprout\Propagator\Tests\Unit\Drivers\Resolvers;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sprout\Propagator\Drivers\Resolvers\PathResolverWizard;
use Sprout\Propagator\Fields\IntegerField;

#[CoversClass(PathResolverWizard::class)]
final class PathResolverWizardTest extends TestCase
{
    #[Test]
    public function it_has_the_correct_name_and_label(): void
    {
        $wizard = new PathResolverWizard();

        $this->assertSame('path', $wizard->getName());
        $this->assertSame('Path', $wizard->getLabel());
    }

    #[Test]
    public function it_defines_a_segment_field(): void
    {
        $wizard = new PathResolverWizard();
        $fields = $wizard->getFields();

        $this->assertCount(1, $fields);

        $segment = $fields[0];
        $this->assertInstanceOf(IntegerField::class, $segment);
        $this->assertSame('segment', $segment->getName());
        $this->assertSame(1, $segment->getDefault());
    }
}
