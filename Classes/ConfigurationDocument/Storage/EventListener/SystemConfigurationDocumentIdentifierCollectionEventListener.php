<?php

namespace DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Storage\EventListener;

use DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Storage\Event\StaticConfigurationDocumentIdentifierCollectionEvent;

class SystemConfigurationDocumentIdentifierCollectionEventListener extends SystemConfigurationDocumentEventListener
{
    public function __invoke(StaticConfigurationDocumentIdentifierCollectionEvent $event): void
    {
        $event->addIdentifiers($this->getIdentifiers());
    }
}
