<?php

namespace DigitalMarketingFramework\Typo3\Core\Updates;

use DigitalMarketingFramework\Typo3\Core\Registry\RegistryCollection;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Registry;
use TYPO3\CMS\Install\Attribute\UpgradeWizard;
use TYPO3\CMS\Install\Updates\ChattyInterface;
use TYPO3\CMS\Install\Updates\RepeatableInterface;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

#[UpgradeWizard('digitalMarketingFramework_migrateConfigurationDocuments')]
final class MigrateConfigurationDocumentsWizard implements
    UpgradeWizardInterface,
    RepeatableInterface,
    ChattyInterface
{
    /**
     * @var string
     */
    public const REGISTRY_NAMESPACE = 'digital_marketing_framework';

    /**
     * @var string
     */
    public const REGISTRY_KEY = 'lastMigratedVersions';

    private OutputInterface $output;

    public function __construct(
        private readonly RegistryCollection $registryCollection,
        private readonly Registry $typo3Registry,
    ) {
    }

    public function getTitle(): string
    {
        return 'Migrate Anyrel configuration documents';
    }

    public function getDescription(): string
    {
        return 'Migrates outdated configuration documents to the current '
            . 'schema version. Documents are processed in children-first '
            . 'order to handle includes correctly.';
    }

    public function setOutput(OutputInterface $output): void
    {
        $this->output = $output;
    }

    public function updateNecessary(): bool
    {
        $schemaDocument = $this->registryCollection->getConfigurationSchemaDocument();
        $currentVersions = $schemaDocument->getVersion();
        $storedVersions = $this->typo3Registry->get(
            self::REGISTRY_NAMESPACE,
            self::REGISTRY_KEY,
            []
        );

        return $currentVersions !== $storedVersions;
    }

    public function executeUpdate(): bool
    {
        $schemaDocument = $this->registryCollection->getConfigurationSchemaDocument();
        $maintenanceService = $this->registryCollection
            ->getConfigurationDocumentMaintenanceService();
        $result = $maintenanceService->migrateAll($schemaDocument);

        foreach ($result['migrated'] as $identifier) {
            $this->output->writeln(sprintf('Migrated: %s', $identifier));
        }

        foreach ($result['skipped'] as $identifier) {
            $this->output->writeln(sprintf('Skipped (readonly): %s', $identifier));
        }

        foreach ($result['failed'] as $identifier => $message) {
            $this->output->writeln(sprintf('Failed: %s — %s', $identifier, $message));
        }

        $this->output->writeln('');
        $this->output->writeln(sprintf(
            'Migrated: %d, Skipped: %d, Failed: %d',
            count($result['migrated']),
            count($result['skipped']),
            count($result['failed'])
        ));

        $success = $result['failed'] === [];

        if ($success) {
            $this->typo3Registry->set(
                self::REGISTRY_NAMESPACE,
                self::REGISTRY_KEY,
                $schemaDocument->getVersion()
            );
        }

        return $success;
    }

    public function getPrerequisites(): array
    {
        return [];
    }
}
