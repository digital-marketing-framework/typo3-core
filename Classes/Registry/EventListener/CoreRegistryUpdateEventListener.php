<?php

namespace DigitalMarketingFramework\Typo3\Core\Registry\EventListener;

use DigitalMarketingFramework\Core\ConfigurationDocument\Parser\YamlConfigurationDocumentParser;
use DigitalMarketingFramework\Core\CoreInitialization;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Storage\StaticConfigurationDocumentStorage;
use DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Storage\YamlFileConfigurationDocumentStorage;
use DigitalMarketingFramework\Typo3\Core\Context\Typo3RequestContext;
use DigitalMarketingFramework\Typo3\Core\Domain\Repository\Api\EndPointRepository;
use DigitalMarketingFramework\Typo3\Core\FileStorage\FileStorage;
use DigitalMarketingFramework\Typo3\Core\GlobalConfiguration\GlobalConfiguration;
use DigitalMarketingFramework\Typo3\Core\Log\LoggerFactory;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Resource\ResourceFactory;

class CoreRegistryUpdateEventListener extends AbstractCoreRegistryUpdateEventListener
{
    public function __construct(
        protected GlobalConfiguration $globalConfiguration,
        protected LoggerFactory $loggerFactory,
        protected Typo3RequestContext $requestContext,
        protected ResourceFactory $resourceFactory,
        protected EventDispatcherInterface $eventDispatcher,
        protected EndPointRepository $endPointStorage,
    ) {
        parent::__construct(new CoreInitialization('dmf_core'));
    }

    protected function initGlobalConfiguration(RegistryInterface $registry): void
    {
        parent::initGlobalConfiguration($registry);
        $registry->setGlobalConfiguration($this->globalConfiguration);
    }

    protected function initServices(RegistryInterface $registry): void
    {
        parent::initServices($registry);

        $registry->setContext($this->requestContext);

        $registry->setLoggerFactory($this->loggerFactory);

        $registry->setEndPointStorage($this->endPointStorage);

        $registry->setFileStorage(
            $registry->createObject(FileStorage::class, [$this->resourceFactory])
        );

        $registry->setConfigurationDocumentStorage(
            $registry->createObject(YamlFileConfigurationDocumentStorage::class)
        );

        $registry->setConfigurationDocumentParser(
            $registry->createObject(YamlConfigurationDocumentParser::class)
        );

        $registry->setStaticConfigurationDocumentStorage(
            $registry->createObject(StaticConfigurationDocumentStorage::class, [$this->eventDispatcher])
        );
    }
}
