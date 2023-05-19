<?php

namespace DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Storage\EventListener;

use DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Storage\Event\StaticConfigurationDocumentLoadEvent;

class SystemConfigurationDocumentLoadEventListener extends SystemConfigurationDocumentEventListener
{
    public function __invoke(StaticConfigurationDocumentLoadEvent $event): void
    {
        $document = $this->getDocument($event->getIdentifier());
        if ($document !== null) {
            $event->setLoadedDocument($document);
        }
    }
}
