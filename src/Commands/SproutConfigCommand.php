<?php
declare(strict_types=1);

namespace Sprout\Propagator\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Config\Repository;
use Sprout\Propagator\Contracts\CategoryRegistry;
use Sprout\Propagator\Contracts\ConfigCategory;
use Sprout\Propagator\Contracts\DriverWizard;
use Sprout\Propagator\Contracts\WizardRenderer;
use Sprout\Propagator\Fields\TextField;
use Sprout\Propagator\Support\ConfigWriter;

/**
 * Sprout Config Command
 *
 * Interactive Artisan command for managing Sprout configuration.
 * Supports add, edit, and delete actions across all registered
 * config categories.
 */
final class SproutConfigCommand extends Command
{
    /**
     * The name and signature of the console command
     *
     * @var string
     */
    protected $signature = 'sprout:config
                            {action? : The action to perform (add, edit, delete)}
                            {category? : The config category (resolvers, providers, etc.)}
                            {name? : The entry name (for edit and delete)}';

    /**
     * The console command description
     *
     * @var string
     */
    protected $description = 'Manage Sprout configuration interactively';

    /**
     * Execute the console command
     *
     * @param CategoryRegistry $registry
     * @param WizardRenderer   $renderer
     * @param Repository       $config
     * @param ConfigWriter     $writer
     *
     * @return int
     */
    public function handle(
        CategoryRegistry $registry,
        WizardRenderer   $renderer,
        Repository       $config,
        ConfigWriter     $writer,
    ): int {
        $action   = $this->resolveAction($renderer);
        $category = $this->resolveCategory($registry, $renderer);

        return match ($action) {
            'add'    => $this->handleAdd($category, $renderer, $config, $writer),
            'edit'   => $this->handleEdit($category, $renderer, $config, $writer),
            'delete' => $this->handleDelete($category, $renderer, $config, $writer),
            default  => self::FAILURE,
        };
    }

    /**
     * Resolve the action from arguments or prompt
     *
     * @param WizardRenderer $renderer
     *
     * @return string
     */
    private function resolveAction(WizardRenderer $renderer): string
    {
        /** @var string|null $action */
        $action = $this->argument('action');

        if (is_string($action)) {
            return $action;
        }

        return $renderer->selectFromList('What would you like to do?', [
            'add'    => 'Add a new entry',
            'edit'   => 'Edit an existing entry',
            'delete' => 'Delete an existing entry',
        ]);
    }

    /**
     * Resolve the category from arguments or prompt
     *
     * @param CategoryRegistry $registry
     * @param WizardRenderer   $renderer
     *
     * @return ConfigCategory
     */
    private function resolveCategory(CategoryRegistry $registry, WizardRenderer $renderer): ConfigCategory
    {
        /** @var string|null $name */
        $name = $this->argument('category');

        if (is_string($name)) {
            return $registry->get($name);
        }

        $options = [];

        foreach ($registry->all() as $cat) {
            $options[$cat->getName()] = $cat->getLabel();
        }

        $selected = $renderer->selectFromList('What would you like to configure?', $options);

        return $registry->get($selected);
    }

    /**
     * Handle the add action
     *
     * @param ConfigCategory $category
     * @param WizardRenderer $renderer
     * @param Repository     $config
     * @param ConfigWriter   $writer
     *
     * @return int
     */
    private function handleAdd(
        ConfigCategory $category,
        WizardRenderer $renderer,
        Repository     $config,
        ConfigWriter   $writer,
    ): int {
        $driverOptions = [];

        foreach ($category->drivers()->all() as $driver) {
            $driverOptions[$driver->getName()] = $driver->getLabel();
        }

        $driverName = $renderer->selectFromList('Which driver?', $driverOptions);
        $driver     = $category->drivers()->get($driverName);

        /** @var string $entryName */
        $entryName = $renderer->renderField(
            TextField::make('name')
                     ->label('Entry name')
                     ->default($driverName)
                     ->required(),
        );

        $fieldValues = $this->collectFieldValues($driver, $renderer);
        $entry       = $category->buildEntry($entryName, $driverName, $fieldValues);

        return $this->applyChanges($category, $entryName, $entry, $config, $writer);
    }

    /**
     * Handle the edit action
     *
     * @param ConfigCategory $category
     * @param WizardRenderer $renderer
     * @param Repository     $config
     * @param ConfigWriter   $writer
     *
     * @return int
     */
    private function handleEdit(
        ConfigCategory $category,
        WizardRenderer $renderer,
        Repository     $config,
        ConfigWriter   $writer,
    ): int {
        $entryName = $this->resolveEntryName($category, $renderer, $config);
        $existing  = $category->getEntry($entryName, $config);

        if ($existing === null) {
            $this->components->error("Entry [{$entryName}] not found.");

            return self::FAILURE;
        }

        /** @var string $driverName */
        $driverName = $existing['driver'] ?? '';
        $driver     = $category->drivers()->get($driverName);

        $fieldValues = $this->collectFieldValues($driver, $renderer, $existing);
        $entry       = $category->buildEntry($entryName, $driverName, $fieldValues);

        return $this->applyChanges($category, $entryName, $entry, $config, $writer);
    }

    /**
     * Handle the delete action
     *
     * @param ConfigCategory $category
     * @param WizardRenderer $renderer
     * @param Repository     $config
     * @param ConfigWriter   $writer
     *
     * @return int
     */
    private function handleDelete(
        ConfigCategory $category,
        WizardRenderer $renderer,
        Repository     $config,
        ConfigWriter   $writer,
    ): int {
        $entryName = $this->resolveEntryName($category, $renderer, $config);
        $existing  = $category->getEntry($entryName, $config);

        if ($existing === null) {
            $this->components->error("Entry [{$entryName}] not found.");

            return self::FAILURE;
        }

        if (! $renderer->confirm("Delete [{$entryName}]?")) {
            $this->components->info('Cancelled.');

            return self::SUCCESS;
        }

        return $this->applyChanges($category, $entryName, null, $config, $writer);
    }

    /**
     * Resolve an entry name from arguments or prompt
     *
     * @param ConfigCategory $category
     * @param WizardRenderer $renderer
     * @param Repository     $config
     *
     * @return string
     */
    private function resolveEntryName(
        ConfigCategory $category,
        WizardRenderer $renderer,
        Repository     $config,
    ): string {
        /** @var string|null $name */
        $name = $this->argument('name');

        if (is_string($name)) {
            return $name;
        }

        $entries = $category->getEntries($config);
        $options = array_combine(array_keys($entries), array_keys($entries));

        return $renderer->selectFromList('Which entry?', $options);
    }

    /**
     * Collect field values from the user
     *
     * @param DriverWizard              $driver
     * @param WizardRenderer            $renderer
     * @param array<string, mixed>|null $currentValues
     *
     * @return array<string, mixed>
     */
    private function collectFieldValues(
        DriverWizard   $driver,
        WizardRenderer $renderer,
        ?array         $currentValues = null,
    ): array {
        $values = [];

        foreach ($driver->getFields() as $field) {
            $dependency = $field->getDependency();

            if ($dependency !== null && ! $dependency->isMet($values)) {
                continue;
            }

            $current                   = $currentValues[$field->getName()] ?? null;
            $values[$field->getName()] = $renderer->renderField($field, $current);
        }

        return $values;
    }

    /**
     * Apply config changes based on the current mode
     *
     * @param ConfigCategory            $category
     * @param string                    $entryName
     * @param array<string, mixed>|null $entry
     * @param Repository                $config
     * @param ConfigWriter              $writer
     *
     * @return int
     */
    private function applyChanges(
        ConfigCategory $category,
        string         $entryName,
        ?array         $entry,
        Repository     $config,
        ConfigWriter   $writer,
    ): int {
        $mode = $config->get('propagator.mode', 'managed');

        if ($mode === 'manual') {
            return $this->displaySnippet($entryName, $entry, $writer);
        }

        return $this->writeConfig($category, $entryName, $entry, $config, $writer);
    }

    /**
     * Display a config snippet for manual mode
     *
     * @param string                    $entryName
     * @param array<string, mixed>|null $entry
     * @param ConfigWriter              $writer
     *
     * @return int
     */
    private function displaySnippet(string $entryName, ?array $entry, ConfigWriter $writer): int
    {
        if ($entry === null) {
            $this->components->info("Remove the '{$entryName}' entry from your config file.");

            return self::SUCCESS;
        }

        $snippet = $writer->render([$entryName => $entry]);
        $this->line($snippet);

        return self::SUCCESS;
    }

    /**
     * Write the config file in managed mode
     *
     * @param ConfigCategory            $category
     * @param string                    $entryName
     * @param array<string, mixed>|null $entry
     * @param Repository                $config
     * @param ConfigWriter              $writer
     *
     * @return int
     */
    private function writeConfig(
        ConfigCategory $category,
        string         $entryName,
        ?array         $entry,
        Repository     $config,
        ConfigWriter   $writer,
    ): int {
        $configFile = $category->getConfigFile();
        $configKey  = $category->getConfigKey();
        $filePath   = config_path($configFile . '.php');

        /** @var array<string, mixed> $fullConfig */
        $fullConfig = $config->get($configFile, []);

        /** @var array<string, mixed> $section */
        $section = $configKey !== '' ? data_get($fullConfig, $configKey, []) : $fullConfig;

        if ($entry === null) {
            unset($section[$entryName]);
        } else {
            $section[$entryName] = $entry;
        }

        if ($configKey !== '') {
            data_set($fullConfig, $configKey, $section);
        } else {
            $fullConfig = $section;
        }

        /** @var array<string, mixed> $fullConfig */
        $output = $writer->render($fullConfig);
        file_put_contents($filePath, $output);

        $action = $entry === null ? 'Removed' : 'Saved';
        $this->components->info("{$action} [{$entryName}] in {$configFile}.php");

        return self::SUCCESS;
    }
}
