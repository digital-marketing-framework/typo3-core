<?php

namespace DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Storage\EventListener;

use DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Storage\Event\StaticConfigurationDocumentIdentifierCollectionEvent;

class StaticConfigurationDocumentIdentifierCollectionEventListener extends StaticConfigurationDocumentEventListener
{
    protected function getExtensionKey(): string
    {
        return 'digitalmarketingframework';
    }

    public function __invoke(StaticConfigurationDocumentIdentifierCollectionEvent $event): void
    {
        $event->addIdentifiers($this->getIdentifiers());
    }
}
