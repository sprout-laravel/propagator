<?php
declare(strict_types=1);

namespace Sprout\Propagator\Tests\Unit\Drivers\Resolvers;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sprout\Propagator\Drivers\Resolvers\HeaderResolverWizard;
use Sprout\Propagator\Fields\TextField;

#[CoversClass(HeaderResolverWizard::class)]
final class HeaderResolverWizardTest extends TestCase
{
    #[Test]
    public function it_has_the_correct_name_and_label(): void
    {
        $wizard = new HeaderResolverWizard();

        $this->assertSame('header', $wizard->getName());
        $this->assertSame('Header', $wizard->getLabel());
    }

    #[Test]
    public function it_defines_a_header_field(): void
    {
        $wizard = new HeaderResolverWizard();
        $fields = $wizard->getFields();

        $this->assertCount(1, $fields);

        $header = $fields[0];
        $this->assertInstanceOf(TextField::class, $header);
        $this->assertSame('header', $header->getName());
        $this->assertSame('{Tenancy}-Identifier', $header->getDefault());
    }
}
