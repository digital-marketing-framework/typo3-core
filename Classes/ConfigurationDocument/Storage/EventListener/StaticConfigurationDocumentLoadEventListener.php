<?php

namespace DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Storage\EventListener;

use DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Storage\Event\StaticConfigurationDocumentLoadEvent;

class StaticConfigurationDocumentLoadEventListener extends StaticConfigurationDocumentEventListener
{
    protected function getExtensionKey(): string
    {
        return 'digitalmarketingframework';
    }

    public function __invoke(StaticConfigurationDocumentLoadEvent $event): void
    {
        $document = $this->getDocument($event->getIdentifier(), $event->getMetaDataOnly());
        if ($document !== null) {
            $event->setLoadedDocument($document);
        }
    }
}
