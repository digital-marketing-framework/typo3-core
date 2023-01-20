<?php

namespace DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Storage;

use DigitalMarketingFramework\Core\ConfigurationDocument\Exception\ConfigurationDocumentNotFoundException;
use DigitalMarketingFramework\Core\ConfigurationDocument\Storage\ConfigurationDocumentStorage as OriginalConfigurationDocumentStorage;
use DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Storage\Event\StaticConfigurationDocumentIdentifierCollectionEvent;
use DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Storage\Event\StaticConfigurationDocumentLoadEvent;
use Exception;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;

abstract class ConfigurationDocumentStorage extends OriginalConfigurationDocumentStorage
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

    public function getDocument(string $documentIdentifier): string
    {
        $event = new StaticConfigurationDocumentLoadEvent($documentIdentifier);
        $this->eventDispatcher->dispatch($event);
        return $event->isLoaded() ? $event->getDocument() : '';
    }
}
