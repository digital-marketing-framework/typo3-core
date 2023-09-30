<?php

namespace DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Storage\EventListener;

use DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Storage\Event\StaticConfigurationDocumentIdentifierCollectionEvent;
use DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Storage\Event\StaticConfigurationDocumentLoadEvent;

abstract class AbstractStaticConfigurationDocumentEventListener
{
    /**
     * @return array<string>
     */
    abstract protected function getIdentifiers(): array;

    abstract protected function getDocument(string $documentIdentifier, bool $metaDataOnly = false): ?string;

    public function __invoke(StaticConfigurationDocumentIdentifierCollectionEvent|StaticConfigurationDocumentLoadEvent $event): void
    {
        if ($event instanceof StaticConfigurationDocumentIdentifierCollectionEvent) {
            $event->addIdentifiers($this->getIdentifiers());
        } else {
            $document = $this->getDocument($event->getIdentifier(), $event->getMetaDataOnly());
            if ($document !== null) {
                $event->setLoadedDocument($document);
            }
        }
    }
}
