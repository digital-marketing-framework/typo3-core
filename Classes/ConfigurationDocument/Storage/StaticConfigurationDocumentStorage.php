<?php

namespace DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Storage;

use DigitalMarketingFramework\Core\ConfigurationDocument\Storage\StaticConfigurationDocumentStorage as OriginalStaticConfigurationDocumentStorage;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareInterface;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareTrait;
use DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Storage\Event\StaticConfigurationDocumentIdentifierCollectionEvent;
use DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Storage\Event\StaticConfigurationDocumentLoadEvent;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;

class StaticConfigurationDocumentStorage extends OriginalStaticConfigurationDocumentStorage implements GlobalConfigurationAwareInterface
{
    use GlobalConfigurationAwareTrait;

    /** @var array<string,mixed> */
    protected array $storageSettings = [];

    public function __construct(
        protected EventDispatcherInterface $eventDispatcher,
    ) {
    }

    /**
     * @return array<string,mixed>
     */
    protected function getStorageSettings(): array
    {
        if (!isset($this->storageSettings)) {
            $this->storageSettings = $this->globalConfiguration->get('core', [])['configurationStorage'] ?? [];
        }

        return $this->storageSettings;
    }

    protected function allowSaveToExtensionPaths(): bool
    {
        return $this->getStorageSettings()['allowSaveToExtensionPaths'] ?? false;
    }

    public function getDocumentIdentifiers(): array
    {
        $event = new StaticConfigurationDocumentIdentifierCollectionEvent();
        $this->eventDispatcher->dispatch($event);

        return $event->getIdentifiers();
    }

    protected function getExtensionFilePath(string $documentIdentifier): ?string
    {
        if (PathUtility::isExtensionPath($documentIdentifier)) {
            $absoluteFilePath = GeneralUtility::getFileAbsFileName($documentIdentifier);
            if ($absoluteFilePath !== '') {
                return $absoluteFilePath;
            }
        }

        return null;
    }

    public function getDocument(string $documentIdentifier, bool $metaDataOnly = false): string
    {
        $event = new StaticConfigurationDocumentLoadEvent($documentIdentifier, $metaDataOnly);
        $this->eventDispatcher->dispatch($event);

        return $event->isLoaded() ? $event->getDocument() : '';
    }

    public function setDocument(string $documentIdentifier, string $document): void
    {
        // TODO should we have a StaticConfigurationDocumentSaveEvent?
        $extensionFilePath = $this->getExtensionFilePath($documentIdentifier);
        if ($extensionFilePath !== null && $this->allowSaveToExtensionPaths()) {
            file_put_contents($extensionFilePath, $document);

            return;
        }

        parent::setDocument($documentIdentifier, $document);
    }

    public function isReadOnly(string $documentIdentifier): bool
    {
        // TODO should we have a StaticConfigurationDocumentReadOnlyEvent?
        $extensionFilePath = $this->getExtensionFilePath($documentIdentifier);
        if ($extensionFilePath !== null && $this->allowSaveToExtensionPaths()) {
            return false;
        }

        return parent::isReadOnly($documentIdentifier);
    }
}
