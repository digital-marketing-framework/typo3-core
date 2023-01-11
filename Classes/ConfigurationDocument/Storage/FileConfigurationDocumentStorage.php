<?php

namespace DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Storage;

use DigitalMarketingFramework\Core\ConfigurationDocument\Exception\ConfigurationDocumentNotFoundException;
use DigitalMarketingFramework\Core\ConfigurationDocument\Storage\ConfigurationDocumentStorage;
use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use TYPO3\CMS\Core\Resource\Exception\ResourceDoesNotExistException;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Resource\ResourceFactory;

abstract class FileConfigurationDocumentStorage extends ConfigurationDocumentStorage
{
    protected function checkFileValidity(File $file): bool
    {
        return preg_match('/.config$/', strtolower($file->getNameWithoutExtension()));
    }

    public function __construct(
        protected ResourceFactory $resourceFactory,
    ) {
    }

    protected function getStorageFolder(): Folder
    {
        $storageConfig = $this->globalConfiguration->get('digitalmarketingframework')['configurationStorage'] ?? [];
        $storageUid = $storageConfig['uid'] ?? '0';
        $storageFolder = $storageConfig['folder'] ?? '/digital_marketing_framework/configuration';
        $folderIdentifier = $storageUid . ':' . $storageFolder;
        try {
            $this->resourceFactory->getFolderObjectFromCombinedIdentifier($folderIdentifier);
        } catch (ResourceDoesNotExistException) {
            // TODO: how to create the folder (including possible parent folders) if it doesn't exist?
            // $this->resourceFactory->createFolderObject($this->resourceFactory->getStorageObject($storageUid), '?', $storageFolder);
            throw new DigitalMarketingFrameworkException(sprintf('Folder "%s" not found.', $folderIdentifier));
        }
    }

    public function getDocumentIdentifiers(): array
    {
        $folder = $this->getStorageFolder();
        $files = $folder->getFiles();
        $identifiers = [];
        /** @var File $file */
        foreach ($files as $file) {
            if ($this->checkFileValidity($file)) {
                $identifiers[] = $file->getCombinedIdentifier();
            }
        }
        return $identifiers;
    }

    protected function getDocumentFile(string $documentIdentifier): File
    {
        $file = $this->resourceFactory->getFileObjectFromCombinedIdentifier($documentIdentifier);
        if ($file === null) {
            throw new ConfigurationDocumentNotFoundException(sprintf('Configuration document not found: %s', $documentIdentifier));
        }
        return $file;
    }

    public function getDocument(string $documentIdentifier): string
    {
        return $this->getDocumentFile($documentIdentifier)->getContents();
    }

    public function setDocument(string $documentIdentifier, string $document): void
    {
        $this->getDocumentFile($documentIdentifier)->setContents($document);
    }
}
