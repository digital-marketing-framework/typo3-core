<?php

namespace DigitalMarketingFramework\Typo3\Core\Command;

use DigitalMarketingFramework\Core\ConfigurationDocument\ConfigurationDocumentMaintenanceServiceInterface;
use DigitalMarketingFramework\Core\Model\ConfigurationDocument\DataSourceMigratable;
use DigitalMarketingFramework\Core\Model\ConfigurationDocument\MigratableInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;
use DigitalMarketingFramework\Typo3\Core\Registry\RegistryCollection;
use DigitalMarketingFramework\Typo3\Core\Updates\MigrateConfigurationDocumentsWizard;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Registry;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * CLI command to migrate all configuration documents.
 */
class MigrateConfigurationCommand extends Command
{
    public function __construct(
        private readonly Registry $typo3Registry,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setHelp('Migrate all outdated configuration documents (storage-backed and data source embedded).');
        $this->addArgument('identifier', InputArgument::OPTIONAL, 'Migrate only the document with this identifier');
        $this->addOption('dry-run', null, InputOption::VALUE_NONE, 'Show what would be migrated without making changes');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $registryCollection = GeneralUtility::makeInstance(RegistryCollection::class);
        $schemaDocument = $registryCollection->getConfigurationSchemaDocument();
        $dryRun = (bool)$input->getOption('dry-run');
        $singleIdentifier = $input->getArgument('identifier');

        $this->displaySchemaVersions($output, $schemaDocument);

        $maintenanceService = $registryCollection->getConfigurationDocumentMaintenanceService();

        if ($singleIdentifier !== null) {
            return $this->executeSingleDocument(
                $output,
                $maintenanceService,
                $schemaDocument,
                $singleIdentifier,
                $dryRun
            );
        }

        $migratables = $maintenanceService->getAllMigratables($schemaDocument);

        $exitCode = $this->executeAll($output, $maintenanceService, $schemaDocument, $migratables, $dryRun);

        if ($exitCode === Command::SUCCESS && !$dryRun) {
            $this->typo3Registry->set(
                MigrateConfigurationDocumentsWizard::REGISTRY_NAMESPACE,
                MigrateConfigurationDocumentsWizard::REGISTRY_KEY,
                $schemaDocument->getVersion()
            );
        }

        return $exitCode;
    }

    protected function displaySchemaVersions(OutputInterface $output, SchemaDocument $schemaDocument): void
    {
        $versions = $schemaDocument->getVersion(true);
        $nonBaselineVersions = array_filter($versions, static fn (string $version) => $version !== '1.0.0');

        $output->writeln('');
        $output->writeln('<info>Schema Versions</info>');
        $output->writeln(str_repeat('-', 50));

        if ($nonBaselineVersions === []) {
            $output->writeln(sprintf('  all %d packages at 1.0.0', count($versions)));
        }

        foreach ($nonBaselineVersions as $package => $version) {
            $output->writeln(sprintf('  %s: %s', $package, $version));
        }
    }

    protected function executeSingleDocument(
        OutputInterface $output,
        ConfigurationDocumentMaintenanceServiceInterface $maintenanceService,
        SchemaDocument $schemaDocument,
        string $identifier,
        bool $dryRun,
    ): int {
        $migratable = $maintenanceService->getMigratableByIdentifier($identifier, $schemaDocument);

        if (!$migratable instanceof MigratableInterface) {
            $output->writeln('');
            $output->writeln(sprintf('<error>Document "%s" not found.</error>', $identifier));
            $output->writeln('');

            return Command::FAILURE;
        }

        $this->displayMigratables($output, [$identifier => $migratable]);

        if (!$migratable->isOutdated()) {
            $output->writeln('');
            $output->writeln('<info>Document is already up to date.</info>');
            $output->writeln('');

            return Command::SUCCESS;
        }

        if ($migratable->isReadOnly()) {
            $output->writeln('');
            $output->writeln('<fg=yellow>Document is readonly — cannot migrate.</>');
            $output->writeln('');

            return Command::FAILURE;
        }

        if ($dryRun) {
            $output->writeln('');
            $output->writeln('<info>Dry run — no changes made.</info>');
            $output->writeln('');

            return Command::SUCCESS;
        }

        $output->writeln('');
        try {
            $migrated = $maintenanceService->migrateDocument($migratable, $schemaDocument);
            if ($migrated) {
                $output->writeln(sprintf('  <fg=green>migrated</> %s', $identifier));
            } else {
                $output->writeln(sprintf('  <comment>no changes</comment> %s', $identifier));
            }
        } catch (Exception $exception) {
            $output->writeln(sprintf('  <fg=red>failed</>   %s: %s', $identifier, $exception->getMessage()));
            $output->writeln('');

            return Command::FAILURE;
        }

        $output->writeln('');

        return Command::SUCCESS;
    }

    /**
     * @param array<string, MigratableInterface> $migratables
     */
    protected function executeAll(
        OutputInterface $output,
        ConfigurationDocumentMaintenanceServiceInterface $maintenanceService,
        SchemaDocument $schemaDocument,
        array $migratables,
        bool $dryRun,
    ): int {
        $this->displayMigratables($output, $migratables);

        $outdatedCount = 0;
        foreach ($migratables as $migratable) {
            if ($migratable->isOutdated()) {
                ++$outdatedCount;
            }
        }

        if ($outdatedCount === 0) {
            $output->writeln('');
            $output->writeln('<info>All documents are up to date.</info>');
            $output->writeln('');

            return Command::SUCCESS;
        }

        if ($dryRun) {
            $output->writeln('');
            $output->writeln('<info>Dry run — no changes made.</info>');
            $output->writeln('');

            return Command::SUCCESS;
        }

        return $this->runMigrations($output, $maintenanceService, $schemaDocument);
    }

    /**
     * @param array<string, MigratableInterface> $migratables
     */
    protected function displayMigratables(OutputInterface $output, array $migratables): void
    {
        $output->writeln('');
        $output->writeln('<info>Configuration Documents</info>');
        $output->writeln(str_repeat('-', 50));

        $outdatedCount = 0;
        $readOnlyCount = 0;

        foreach ($migratables as $migratable) {
            $flags = [];
            if ($migratable->isReadOnly()) {
                $flags[] = 'readonly';
                ++$readOnlyCount;
            }

            if ($migratable->isOutdated()) {
                $flags[] = '<fg=yellow>outdated</>';
                ++$outdatedCount;
            }

            if ($migratable instanceof DataSourceMigratable) {
                $flags[] = 'data-source';
            }

            $flagStr = $flags !== [] ? ' [' . implode(', ', $flags) . ']' : '';
            $includes = $migratable->getIncludes() !== [] ? ' includes=[' . implode(', ', $migratable->getIncludes()) . ']' : '';
            $includedBy = $migratable->getIncludedBy() !== [] ? ' includedBy=[' . implode(', ', $migratable->getIncludedBy()) . ']' : '';

            $output->writeln(sprintf(
                '  <comment>%s</comment> "%s"%s%s%s',
                $migratable->getIdentifier(),
                $migratable->getName(),
                $flagStr,
                $includes,
                $includedBy
            ));

            if ($migratable->isOutdated() && $migratable->getMigrationInfo() !== []) {
                foreach ($migratable->getMigrationInfo() as $package => $info) {
                    $from = $info['from'] !== '' ? $info['from'] : '1.0.0';
                    $color = match ($info['status']) {
                        'error' => 'red',
                        'genuine' => 'yellow',
                        default => 'gray',
                    };
                    $line = sprintf('%s: %s → %s', $package, $from, $info['to']);
                    if ($info['message'] !== '') {
                        $line .= ' — ' . $info['message'];
                    }

                    $output->writeln(sprintf('    <fg=%s>%s</>', $color, $line));
                }
            }
        }

        $output->writeln('');
        $output->writeln(sprintf(
            '  Total: %d documents, %d readonly, %d outdated',
            count($migratables),
            $readOnlyCount,
            $outdatedCount
        ));
    }

    protected function runMigrations(
        OutputInterface $output,
        ConfigurationDocumentMaintenanceServiceInterface $maintenanceService,
        SchemaDocument $schemaDocument,
    ): int {
        $output->writeln('');
        $output->writeln('<info>Running migrations...</info>');
        $output->writeln(str_repeat('-', 50));

        $result = $maintenanceService->migrateAll($schemaDocument);

        foreach ($result['migrated'] as $identifier) {
            $output->writeln(sprintf('  <fg=green>migrated</> %s', $identifier));
        }

        foreach ($result['skipped'] as $identifier) {
            $output->writeln(sprintf('  <fg=yellow>skipped</>  %s (readonly)', $identifier));
        }

        foreach ($result['failed'] as $identifier => $message) {
            $output->writeln(sprintf('  <fg=red>failed</>   %s: %s', $identifier, $message));
        }

        $output->writeln('');
        $output->writeln(sprintf(
            '  Migrated: %d, Skipped: %d, Failed: %d',
            count($result['migrated']),
            count($result['skipped']),
            count($result['failed'])
        ));
        $output->writeln('');

        return $result['failed'] !== [] ? Command::FAILURE : Command::SUCCESS;
    }
}
