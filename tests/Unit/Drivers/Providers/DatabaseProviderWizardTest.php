<?php
declare(strict_types=1);

namespace Sprout\Propagator\Tests\Unit\Drivers\Providers;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sprout\Propagator\Drivers\Providers\DatabaseProviderWizard;
use Sprout\Propagator\Fields\ClassField;
use Sprout\Propagator\Fields\TextField;

#[CoversClass(DatabaseProviderWizard::class)]
final class DatabaseProviderWizardTest extends TestCase
{
    #[Test]
    public function it_has_the_correct_name_and_label(): void
    {
        $wizard = new DatabaseProviderWizard();

        $this->assertSame('database', $wizard->getName());
        $this->assertSame('Database', $wizard->getLabel());
    }

    #[Test]
    public function it_defines_table_entity_and_connection_fields(): void
    {
        $wizard = new DatabaseProviderWizard();
        $fields = $wizard->getFields();

        $this->assertCount(3, $fields);

        $table = $fields[0];
        $this->assertInstanceOf(TextField::class, $table);
        $this->assertSame('table', $table->getName());
        $this->assertTrue($table->isRequired());

        $entity = $fields[1];
        $this->assertInstanceOf(ClassField::class, $entity);
        $this->assertSame('entity', $entity->getName());
        $this->assertFalse($entity->isRequired());

        $connection = $fields[2];
        $this->assertInstanceOf(TextField::class, $connection);
        $this->assertSame('connection', $connection->getName());
        $this->assertFalse($connection->isRequired());
    }
}
