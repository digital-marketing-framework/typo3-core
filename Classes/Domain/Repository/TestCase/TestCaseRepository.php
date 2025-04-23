<?php

namespace DigitalMarketingFramework\Typo3\Core\Domain\Repository\TestCase;

use DigitalMarketingFramework\Core\TestCase\TestCaseStorageInterface;
use DigitalMarketingFramework\Typo3\Core\GlobalConfiguration\Schema\CoreGlobalConfigurationSchema;
use DigitalMarketingFramework\Typo3\Core\Domain\Model\TestCase\TestCase;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * @extends Repository<TestCase>
 */
class TestCaseRepository extends Repository implements TestCaseStorageInterface
{
    protected int $pid;

    public function __construct(
        protected ExtensionConfiguration $extensionConfiguration,
    ) {
        $typo3Version = new Typo3Version();
        if ($typo3Version->getMajorVersion() <= 11) {
            $objectManager = GeneralUtility::makeInstance(ObjectManager::class); // @phpstan-ignore-line TYPO3 version switch
            parent::__construct($objectManager); // @phpstan-ignore-line TYPO3 version switch
        } else {
            parent::__construct(); // @phpstan-ignore-line TYPO3 version switch
        }
    }

    public function getPid(): int
    {
        if (!isset($this->pid)) {
            try {
                $config = $this->extensionConfiguration->get('dmf_core');
                $this->pid = $config[CoreGlobalConfigurationSchema::KEY_TESTS][CoreGlobalConfigurationSchema::KEY_TESTS_STORAGE_PID]
                    ?? CoreGlobalConfigurationSchema::DEFAULT_TESTS_STORAGE_PID;
            } catch (ExtensionConfigurationExtensionNotConfiguredException|ExtensionConfigurationPathDoesNotExistException) {
                $this->pid = CoreGlobalConfigurationSchema::DEFAULT_TESTS_STORAGE_PID;
            }
        }

        return $this->pid;
    }

    public function createQuery(): QueryInterface
    {
        $query = parent::createQuery();
        $query->getQuerySettings()->setRespectStoragePage(true);
        $query->getQuerySettings()->setStoragePageIds([$this->getPid()]);

        return $query;
    }

    public function getAllTestCases(): array
    {
        $query = $this->createQuery();

        return $query->execute()->toArray();
    }

    public function getTypeSpecificTestCases(string $type): array
    {
        return $this->findByType($type)->toArray();
    }
}
