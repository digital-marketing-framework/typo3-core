<?php

namespace DigitalMarketingFramework\Typo3\Core\Registry\EventListener;

use DigitalMarketingFramework\Core\ConfigurationDocument\Parser\YamlConfigurationDocumentParser;
use DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Storage\YamlFileConfigurationDocumentStorage;
use DigitalMarketingFramework\Typo3\Core\Context\Typo3RequestContext;
use DigitalMarketingFramework\Typo3\Core\Log\LoggerFactory;
use DigitalMarketingFramework\Typo3\Core\Registry\Event\CoreRegistryServiceUpdateEvent;
use TYPO3\CMS\Core\Resource\ResourceFactory;

class CoreRegistryServiceUpdateEventListener
{
    public function __construct(
        protected LoggerFactory $loggerFactory,
        protected Typo3RequestContext $requestContext,
        protected ResourceFactory $resourceFactory,
    ) {}

    public function __invoke(CoreRegistryServiceUpdateEvent $event): void
    {
        $registry = $event->getRegistry();
        $registry->setContext($this->requestContext);
        $registry->setLoggerFactory($this->loggerFactory);

        $registry->setConfigurationDocumentStorage(
            $registry->createObject(YamlFileConfigurationDocumentStorage::class, [$this->resourceFactory])
        );
        $registry->setConfigurationDocumentParser(
            $registry->createObject(YamlConfigurationDocumentParser::class)
        );
    }
}
