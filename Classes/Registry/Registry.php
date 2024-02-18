<?php

namespace DigitalMarketingFramework\Typo3\Core\Registry;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\SchemaDocument;
use DigitalMarketingFramework\Core\Registry\Registry as CoreRegistry;
use DigitalMarketingFramework\Core\Registry\RegistryUpdateType;
use DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Event\ConfigurationDocumentMetaDataUpdateEvent;
use DigitalMarketingFramework\Typo3\Core\Registry\Event\CoreRegistryUpdateEvent;
use InvalidArgumentException;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\SingletonInterface;

class Registry extends CoreRegistry implements SingletonInterface
{
    public const SCHEMA_KEY_CONFIGURATION_DOCUMENT = 'configurationDocument';

    public const SCHEMA_KEY_GLOBAL_CONFIGURATION = 'globalConfiguration';

    public function __construct(
        protected EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function initializeObject(): void
    {
        $this->eventDispatcher->dispatch(
            new CoreRegistryUpdateEvent($this, RegistryUpdateType::GLOBAL_CONFIGURATION)
        );
        $this->eventDispatcher->dispatch(
            new CoreRegistryUpdateEvent($this, RegistryUpdateType::SERVICE)
        );
        $this->eventDispatcher->dispatch(
            new CoreRegistryUpdateEvent($this, RegistryUpdateType::PLUGIN)
        );
    }

    public function getSchemaDocument(string $key = self::SCHEMA_KEY_CONFIGURATION_DOCUMENT): SchemaDocument
    {
        $event = new ConfigurationDocumentMetaDataUpdateEvent();
        $this->eventDispatcher->dispatch($event);
        switch ($key) {
            case static::SCHEMA_KEY_CONFIGURATION_DOCUMENT:
                return $event->getSchemaDocument();
                break;
            case static::SCHEMA_KEY_GLOBAL_CONFIGURATION:
                return $event->getGlobalConfigurationSchemaDocument();
                break;
            default:
                throw new InvalidArgumentException(sprintf('Unknown schema key "%s".', $key));
        }
    }
}
