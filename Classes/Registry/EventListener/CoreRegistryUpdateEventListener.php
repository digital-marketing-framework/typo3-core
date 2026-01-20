<?php

namespace DigitalMarketingFramework\Typo3\Core\Registry\EventListener;

use DigitalMarketingFramework\Core\ConfigurationDocument\Parser\YamlConfigurationDocumentParser;
use DigitalMarketingFramework\Core\CoreInitialization;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Typo3\Core\Backend\AssetUriBuilder;
use DigitalMarketingFramework\Typo3\Core\Backend\Controller\SectionController\ApiEditSectionController;
use DigitalMarketingFramework\Typo3\Core\Backend\Controller\SectionController\TestsEditSectionController;
use DigitalMarketingFramework\Typo3\Core\Backend\UriBuilder;
use DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Storage\YamlFileConfigurationDocumentStorage;
use DigitalMarketingFramework\Typo3\Core\Domain\Repository\Api\EndPointRepository;
use DigitalMarketingFramework\Typo3\Core\Domain\Repository\TestCase\TestCaseRepository;
use DigitalMarketingFramework\Typo3\Core\FileStorage\FileStorage;
use DigitalMarketingFramework\Typo3\Core\GlobalConfiguration\GlobalConfiguration;
use DigitalMarketingFramework\Typo3\Core\GlobalConfiguration\Schema\CoreGlobalConfigurationSchema;
use DigitalMarketingFramework\Typo3\Core\Log\LoggerFactory;
use DigitalMarketingFramework\Typo3\Core\Resource\ExtensionResourceService;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Resource\ResourceFactory;

class CoreRegistryUpdateEventListener extends AbstractCoreRegistryUpdateEventListener
{
    public function __construct(
        protected ExtensionConfiguration $extensionConfiguration,
        protected LoggerFactory $loggerFactory,
        protected ResourceFactory $resourceFactory,
        protected EventDispatcherInterface $eventDispatcher,
        protected EndPointRepository $endPointStorage,
        protected TestCaseRepository $testCaseRepository,
    ) {
        $initialization = new CoreInitialization('dmf_core');
        $initialization->setGlobalConfigurationSchema(new CoreGlobalConfigurationSchema());
        parent::__construct($initialization);
    }

    protected function initGlobalConfiguration(RegistryInterface $registry): void
    {
        parent::initGlobalConfiguration($registry);

        $globalConfiguration = new GlobalConfiguration($registry, $this->extensionConfiguration);
        $registry->setGlobalConfiguration($globalConfiguration);
    }

    protected function initServices(RegistryInterface $registry): void
    {
        $registry->setLoggerFactory($this->loggerFactory);

        $registry->setFileStorage(
            $registry->createObject(FileStorage::class, [$this->resourceFactory])
        );

        $registry->setConfigurationDocumentStorage(
            $registry->createObject(YamlFileConfigurationDocumentStorage::class)
        );

        $registry->setConfigurationDocumentParser(
            $registry->createObject(YamlConfigurationDocumentParser::class)
        );

        $this->endPointStorage->setGlobalConfiguration($registry->getGlobalConfiguration());
        $registry->setEndPointStorage($this->endPointStorage);

        $this->testCaseRepository->setGlobalConfiguration($registry->getGlobalConfiguration());
        $registry->setTestCaseStorage($this->testCaseRepository);

        $vendorResourceService = $registry->getVendorResourceService();
        $vendorResourceService->setVendorPath(Environment::getProjectPath() . '/vendor');

        $assetService = $registry->getAssetService();
        $assetService->setAssetConfig([
            'tempBasePath' => Environment::getPublicPath() . '/typo3temp',
            'publicTempBasePath' => 'typo3temp',
            'salt' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey'],
        ]);

        $extensionResourceService = $registry->createObject(ExtensionResourceService::class);
        $registry->registerResourceService($extensionResourceService);

        $registry->setBackendUriBuilder(
            $registry->createObject(UriBuilder::class)
        );
        $registry->setBackendAssetUriBuilder(
            $registry->createObject(AssetUriBuilder::class, [$registry])
        );

        parent::initServices($registry);
    }

    protected function initPlugins(RegistryInterface $registry): void
    {
        parent::initPlugins($registry);
        $registry->registerBackendSectionController(ApiEditSectionController::class);
        $registry->registerBackendSectionController(TestsEditSectionController::class);
    }
}
