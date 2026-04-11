<?php
declare(strict_types=1);

namespace Sprout\Propagator\Tests\Unit\Drivers\Resolvers;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sprout\Propagator\Drivers\Resolvers\CookieResolverWizard;
use Sprout\Propagator\Fields\GroupField;
use Sprout\Propagator\Fields\TextField;

#[CoversClass(CookieResolverWizard::class)]
final class CookieResolverWizardTest extends TestCase
{
    #[Test]
    public function it_has_the_correct_name_and_label(): void
    {
        $wizard = new CookieResolverWizard();

        $this->assertSame('cookie', $wizard->getName());
        $this->assertSame('Cookie', $wizard->getLabel());
    }

    #[Test]
    public function it_defines_cookie_and_options_fields(): void
    {
        $wizard = new CookieResolverWizard();
        $fields = $wizard->getFields();

        $this->assertCount(2, $fields);

        $cookie = $fields[0];
        $this->assertInstanceOf(TextField::class, $cookie);
        $this->assertSame('cookie', $cookie->getName());
        $this->assertSame('{Tenancy}-Identifier', $cookie->getDefault());

        $options = $fields[1];
        $this->assertInstanceOf(GroupField::class, $options);
        $this->assertSame('options', $options->getName());

        $nested = $options->getFields();
        $this->assertCount(2, $nested);
        $this->assertSame('path', $nested[0]->getName());
        $this->assertSame('/', $nested[0]->getDefault());
        $this->assertSame('domain', $nested[1]->getName());
    }
}
