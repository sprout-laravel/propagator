<?php
declare(strict_types=1);

namespace Sprout\Propagator\Tests\Unit\Drivers\Resolvers;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sprout\Propagator\Drivers\Resolvers\SubdomainResolverWizard;
use Sprout\Propagator\Fields\EnvField;
use Sprout\Propagator\Fields\TextField;

#[CoversClass(SubdomainResolverWizard::class)]
final class SubdomainResolverWizardTest extends TestCase
{
    #[Test]
    public function it_has_the_correct_name_and_label(): void
    {
        $wizard = new SubdomainResolverWizard();
        $this->assertSame('subdomain', $wizard->getName());
        $this->assertSame('Subdomain', $wizard->getLabel());
    }

    #[Test]
    public function it_defines_domain_and_pattern_fields(): void
    {
        $wizard = new SubdomainResolverWizard();
        $fields = $wizard->getFields();
        $this->assertCount(2, $fields);

        $domain = $fields[0];
        $this->assertInstanceOf(EnvField::class, $domain);
        $this->assertSame('domain', $domain->getName());
        $this->assertTrue($domain->isRequired());

        $pattern = $fields[1];
        $this->assertInstanceOf(TextField::class, $pattern);
        $this->assertSame('pattern', $pattern->getName());
        $this->assertSame('.*', $pattern->getDefault());
    }
}
