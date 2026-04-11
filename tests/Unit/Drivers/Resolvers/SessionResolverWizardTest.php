<?php
declare(strict_types=1);

namespace Sprout\Propagator\Tests\Unit\Drivers\Resolvers;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sprout\Propagator\Drivers\Resolvers\SessionResolverWizard;
use Sprout\Propagator\Fields\TextField;

#[CoversClass(SessionResolverWizard::class)]
final class SessionResolverWizardTest extends TestCase
{
    #[Test]
    public function it_has_the_correct_name_and_label(): void
    {
        $wizard = new SessionResolverWizard();

        $this->assertSame('session', $wizard->getName());
        $this->assertSame('Session', $wizard->getLabel());
    }

    #[Test]
    public function it_defines_a_session_field(): void
    {
        $wizard = new SessionResolverWizard();
        $fields = $wizard->getFields();

        $this->assertCount(1, $fields);

        $session = $fields[0];
        $this->assertInstanceOf(TextField::class, $session);
        $this->assertSame('session', $session->getName());
        $this->assertSame('multitenancy.{tenancy}', $session->getDefault());
    }
}
