<?php

namespace DigitalMarketingFramework\Typo3\Core\Domain\Repository\TestCase;

use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationInterface;
use DigitalMarketingFramework\Core\Model\TestCase\TestCase;
use DigitalMarketingFramework\Core\Model\TestCase\TestCaseInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\TestCase\TestCaseSchema;
use DigitalMarketingFramework\Core\TestCase\TestCaseStorageInterface;
use DigitalMarketingFramework\Typo3\Core\Domain\Repository\ItemStorageRepository;
use DigitalMarketingFramework\Typo3\Core\TestCase\GlobalConfiguration\Settings\TestCaseSettings;
use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 * @extends ItemStorageRepository<TestCaseInterface>
 */
class TestCaseRepository extends ItemStorageRepository implements TestCaseStorageInterface
{
    public function __construct(ConnectionPool $connectionPool)
    {
        parent::__construct($connectionPool, TestCase::class, 'tx_dmfcore_domain_model_test_case');
    }

    public function getPid(): int
    {
        if ($this->pid === null) {
            if ($this->globalConfiguration instanceof GlobalConfigurationInterface) {
                $testCaseSettings = $this->globalConfiguration->getGlobalSettings(TestCaseSettings::class);
                $this->pid = $testCaseSettings->getPid();
            } else {
                $this->pid = 0;
            }
        }

        return $this->pid;
    }

    public function fetchByType(string $type): array
    {
        return $this->fetchFiltered(['type' => $type]);
    }

    public function fetchByName(string $name): array
    {
        return $this->fetchFiltered(['name' => $name]);
    }

    public function fetchAllTypes(): array
    {
        // TODO fetch all types from DB
        return ['distributor'];
    }

    public static function getSchema(): ContainerSchema
    {
        return new TestCaseSchema();
    }
}
