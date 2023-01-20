<?php

namespace DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Storage;

use DigitalMarketingFramework\Core\ConfigurationDocument\Exception\ConfigurationDocumentNotFoundException;
use Exception;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;

abstract class FileConfigurationDocumentStorage extends ConfigurationDocumentStorage
{
    protected const ACCESS_FILE_PATH = 'EXT:digitalmarketingframework/Resources/Private/StaticTemplates/.htaccess';

    protected array $storageConfiguration;
    protected ResourceStorage $storage;
    protected Folder $storageFolder;

    protected function checkFileValidity(File $file): bool
    {
        return preg_match('/.config$/', strtolower($file->getNameWithoutExtension()));
    }

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        protected ResourceFactory $resourceFactory,
    ) {
        parent::__construct($eventDispatcher);
    }

    protected function getFile(string $identifier): File
    {
        return $this->resourceFactory->retrieveFileOrFolderObject($identifier);
    }

    protected function getFileContents(string $identifier): string
    {
        try {
            if (PathUtility::isExtensionPath($identifier)) {
                $absoluteFilePath = GeneralUtility::getFileAbsFileName($identifier);
                if ($absoluteFilePath === '') {
                    throw new ConfigurationDocumentNotFoundException(sprintf('Configuration document identifier "%s" seems to be invalid.', $identifier));
                }
                return file_get_contents($absoluteFilePath);
            } else {
                return $this->getFile($identifier)->getContents();
            }
        } catch (Exception $e) {
            throw new ConfigurationDocumentNotFoundException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function isReadOnly(string $identifier): bool
    {
        return PathUtility::isExtensionPath($identifier);
    }

    protected function getAccessFileContents(): string
    {
        return $this->getFileContents(static::ACCESS_FILE_PATH);
    }

    protected function updateAccessFile(?Folder $folder = null): void
    {
        if ($folder === null) {
            $folder = $this->getStorageFolder();
        }
        if (!$folder->hasFile('.htaccess')) {
            try {
                $accessFileContents = $this->getAccessFileContents();
                $file = $folder->createFile('.htaccess');
                $file->setContents($accessFileContents);
            } catch (Exception $e) {
                $this->logger->warning(sprintf('Unable to create .htaccess file for folder "%s"', $folder->getIdentifier()));
            }
        }
    }

    protected function getStorageConfiguration(): array
    {
        if (!isset($this->storageConfiguration)) {
            $this->storageConfiguration = $this->globalConfiguration->get('digitalmarketingframework')['configurationStorage'] ?? [];
        }
        return $this->storageConfiguration;
    }

    protected function getStorage(): ResourceStorage
    {
        if (!isset($this->storage)) {
            $storageUid = $this->getStorageConfiguration()['uid'] ?? '0';
            $this->storage = $this->resourceFactory->getStorageObject($storageUid);
        }
        return $this->storage;
    }

    protected function getStorageFolder(): Folder
    {
        if (!isset($this->storageFolder)) {
            $storage = $this->getStorage();
            $path = $this->getStorageConfiguration()['folder'] ?? '/digital_marketing_framework/configuration';
            try {
                $this->storageFolder = $storage->getFolder($path);
            } catch (Exception) {
                try {
                    $this->storageFolder = $storage->createFolder($path);
                } catch (Exception $e) {
                    throw new DigitalMarketingException($e->getMessage(), $e->getCode(), $e);
                }
                $this->updateAccessFile($this->storageFolder);
            }
        }
        return $this->storageFolder;
    }

    public function getDocumentIdentifiers(): array
    {
        $identifiers = parent::getDocumentIdentifiers();
        $folder = $this->getStorageFolder();
        $files = $folder->getFiles();
        foreach ($files as $file) {
            if ($this->checkFileValidity($file)) {
                $identifiers[] = $file->getCombinedIdentifier();
            }
        }
        return $identifiers;
    }

    public function getDocument(string $documentIdentifier): string
    {
        $document = parent::getDocument($documentIdentifier);
        if ($document === '') {
            $document = $this->getFileContents($documentIdentifier);
        }
        return $document;
    }

    public function setDocument(string $documentIdentifier, string $document): void
    {
        $this->getFile($documentIdentifier)->setContents($document);
    }

    public function initalizeConfigurationDocumentStorage(): void
    {
        $folder = $this->getStorageFolder();
        $this->updateAccessFile($folder);
    }
}
