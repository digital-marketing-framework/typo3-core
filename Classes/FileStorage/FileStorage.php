<?php

namespace DigitalMarketingFramework\Typo3\Core\FileStorage;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\FileStorage\FileStorageInterface;
use DigitalMarketingFramework\Core\Log\LoggerAwareInterface;
use DigitalMarketingFramework\Core\Log\LoggerAwareTrait;
use Exception;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Resource\Exception\ResourceDoesNotExistException;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FileStorage implements FileStorageInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(
        protected ResourceFactory $resourceFactory,
    ) {
    }

    protected function getResource(string $identifier): null|File|Folder
    {
        try {
            return $this->resourceFactory->retrieveFileOrFolderObject($identifier);
        } catch (ResourceDoesNotExistException $e) {
            $this->logger->error(sprintf('Resource does not exist: %s', $e->getMessage()));

            return null;
        }
    }

    protected function getFile(string $identifier): ?File
    {
        $file = $this->getResource($identifier);
        if ($file instanceof File) {
            return $file;
        }

        return null;
    }

    protected function getFolder(string $identifier): ?Folder
    {
        $file = $this->getResource($identifier);
        if ($file instanceof Folder) {
            return $file;
        }

        return null;
    }

    public function getFileContents(string $fileIdentifier): ?string
    {
        try {
            $file = $this->getFile($fileIdentifier);
            if (!$file instanceof File) {
                $this->logger->warning(sprintf('File %s does not seem to exist.', $fileIdentifier));

                return null;
            }

            return $file->getContents();
        } catch (Exception) {
            $this->logger->warning(sprintf('File %s does not seem to exist.', $fileIdentifier));

            return null;
        }
    }

    public function putFileContents(string $fileIdentifier, string $fileContent): void
    {
        $file = $this->getFile($fileIdentifier);
        if (!$file instanceof File) {
            [$storageUid, $filePath] = explode(':', $fileIdentifier);
            $pathinfo = pathinfo($filePath);
            $folder = $this->getFolder($storageUid . ':' . $pathinfo['dirname']);
            $file = $folder->createFile($pathinfo['basename']);
        }

        $file->setContents($fileContent);
    }

    public function deleteFile(string $fileIdentifier): void
    {
        $this->getFile($fileIdentifier)?->delete();
    }

    public function getFileName(string $fileIdentifier): ?string
    {
        return $this->getFile($fileIdentifier)?->getName();
    }

    public function getFileBaseName(string $fileIdentifier): ?string
    {
        return $this->getFile($fileIdentifier)?->getNameWithoutExtension();
    }

    public function getFileExtension(string $fileIdentifier): ?string
    {
        return $this->getFile($fileIdentifier)?->getExtension();
    }

    public function fileExists(string $fileIdentifier): bool
    {
        return $this->getFileContents($fileIdentifier) !== null;
    }

    public function fileIsReadOnly(string $fileIdentifier): bool
    {
        $file = $this->getFile($fileIdentifier);
        if ($file instanceof File) {
            return !$file->checkActionPermission('write');
        }

        return false;
    }

    public function fileIsWriteable(string $fileIdentifier): bool
    {
        return !$this->fileIsReadOnly($fileIdentifier);
    }

    public function getFilesFromFolder(string $folderIdentifier): array
    {
        $folder = $this->getFolder($folderIdentifier);
        if (!$folder instanceof Folder) {
            return [];
        }

        $list = [];
        foreach ($folder->getFiles() as $file) {
            $list[] = $file->getCombinedIdentifier();
        }

        return $list;
    }

    public function folderExists(string $folderIdentifier): bool
    {
        return $this->getFolder($folderIdentifier) instanceof Folder;
    }

    public function createFolder(string $folderIdentifier): void
    {
        if (!$this->folderExists($folderIdentifier)) {
            $identifierParts = explode(':', $folderIdentifier);
            $storageUid = (int)array_shift($identifierParts);
            $path = implode(':', $identifierParts);
            $storage = $this->resourceFactory->getStorageObject($storageUid);
            try {
                $storage->createFolder($path);
            } catch (Exception $e) {
                throw new DigitalMarketingFrameworkException($e->getMessage(), $e->getCode(), $e);
            }
        }
    }

    public function getTempPath(): string
    {
        return Environment::getVarPath() . '/transient/';
    }

    public function writeTempFile(string $filePrefix = '', string $fileContent = '', string $fileSuffix = ''): string|bool
    {
        $result = null;
        $fileIdentifier = GeneralUtility::tempnam($filePrefix, $fileSuffix);
        if ($this->fileIsWriteable($fileIdentifier)) {
            $result = file_put_contents($fileIdentifier, $fileContent);
        } else {
            $this->logger->warning(sprintf('File %s does not seem to be writeable.', $fileIdentifier));
        }

        return $result ? $fileIdentifier : false;
    }
}
