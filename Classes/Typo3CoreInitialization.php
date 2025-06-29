<?php

namespace DigitalMarketingFramework\Typo3\Core;

use DigitalMarketingFramework\Core\Backend\Controller\SectionController\SectionControllerInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\Parser\YamlConfigurationDocumentParser;
use DigitalMarketingFramework\Core\CoreInitialization as OriginalCoreInitialization;
use DigitalMarketingFramework\Core\Registry\RegistryDomain;
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

class Typo3CoreInitialization extends OriginalCoreInitialization
{
    public function __construct(
        protected ExtensionConfiguration $extensionConfiguration,
        protected LoggerFactory $loggerFactory,
        protected ResourceFactory $resourceFactory,
        protected EventDispatcherInterface $eventDispatcher,
        protected EndPointRepository $endPointStorage,
        protected TestCaseRepository $testCaseRepository,
    ) {
        parent::__construct('dmf_core', new CoreGlobalConfigurationSchema());
    }

    protected function getPluginDefinitions(): array
    {
        $pluginDefinitions = parent::getPluginDefinitions();
        $pluginDefinitions[RegistryDomain::CORE][SectionControllerInterface::class][] = ApiEditSectionController::class;
        $pluginDefinitions[RegistryDomain::CORE][SectionControllerInterface::class][] = TestsEditSectionController::class;

        return $pluginDefinitions;
    }

    /**
     * Minimum system to make the global configuration work
     */
    protected function bootstrap(RegistryInterface $registry): void
    {
        $registry->setLoggerFactory($this->loggerFactory);

        $registry->setFileStorage(
            $registry->createObject(FileStorage::class, [$this->resourceFactory])
        );

        $registry->setConfigurationDocumentParser(
            $registry->createObject(YamlConfigurationDocumentParser::class)
        );
    }

    public function initGlobalConfiguration(string $domain, RegistryInterface $registry): void
    {
        parent::initGlobalConfiguration($domain, $registry);

        if ($domain !== RegistryDomain::CORE) {
            return;
        }

        $this->bootstrap($registry);

        $globalConfiguration = new GlobalConfiguration($registry, $this->extensionConfiguration);
        $registry->setGlobalConfiguration($globalConfiguration);
    }

    public function initServices(string $domain, RegistryInterface $registry): void
    {
        parent::initServices($domain, $registry);

        if ($domain !== RegistryDomain::CORE) {
            return;
        }

        $registry->setConfigurationDocumentStorage(
            $registry->createObject(YamlFileConfigurationDocumentStorage::class)
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

        $registry->setBackendUriBuilder(new UriBuilder());
        $registry->setBackendAssetUriBuilder(new AssetUriBuilder($registry));
    }
}
