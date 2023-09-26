<?php

namespace DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Storage;

use DigitalMarketingFramework\Core\ConfigurationDocument\Storage\StaticConfigurationDocumentStorage as OriginalStaticConfigurationDocumentStorage;
use DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Storage\Event\StaticConfigurationDocumentIdentifierCollectionEvent;
use DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Storage\Event\StaticConfigurationDocumentLoadEvent;
use Psr\EventDispatcher\EventDispatcherInterface;

class StaticConfigurationDocumentStorage extends OriginalStaticConfigurationDocumentStorage
{
    public function __construct(
        protected EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function getDocumentIdentifiers(): array
    {
        $event = new StaticConfigurationDocumentIdentifierCollectionEvent();
        $this->eventDispatcher->dispatch($event);

        return $event->getIdentifiers();
    }

    public function getDocument(string $documentIdentifier, bool $metaDataOnly = false): string
    {
        $event = new StaticConfigurationDocumentLoadEvent($documentIdentifier, $metaDataOnly);
        $this->eventDispatcher->dispatch($event);

        return $event->isLoaded() ? $event->getDocument() : '';
    }
}
