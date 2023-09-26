<?php

namespace DigitalMarketingFramework\Typo3\Core\FileStorage;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\FileStorage\FileStorageInterface;
use DigitalMarketingFramework\Core\Log\LoggerAwareInterface;
use DigitalMarketingFramework\Core\Log\LoggerAwareTrait;
use Exception;
use TYPO3\CMS\Core\Resource\Exception\ResourceDoesNotExistException;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;

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

        return null;
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
            if (PathUtility::isExtensionPath($fileIdentifier)) {
                $absoluteFilePath = GeneralUtility::getFileAbsFileName($fileIdentifier);
                if ($absoluteFilePath === '') {
                    $this->logger->warning(sprintf('File %s does not seem to exist.', $fileIdentifier));

                    return null;
                }

                return file_get_contents($absoluteFilePath);
            } else {
                $file = $this->getFile($fileIdentifier);
                if ($file === null) {
                    $this->logger->warning(sprintf('File %s does not seem to exist.', $fileIdentifier));

                    return null;
                }

                return $file->getContents();
            }
        } catch (Exception $e) {
            $this->logger->warning(sprintf('File %s does not seem to exist.', $fileIdentifier));

            return null;
        }
    }

    public function putFileContents(string $fileIdentifier, string $fileContent): void
    {
        $file = $this->getFile($fileIdentifier);
        if ($file === null) {
            [$storageUid, $filePath] = explode(':', $fileIdentifier);
            $pathinfo = pathinfo($filePath);
            $folder = $this->getFolder($storageUid.':'.$pathinfo['dirname']);
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
        if (PathUtility::isExtensionPath($fileIdentifier)) {
            return true;
        }
        if (preg_match('/^[^0-9]+:/', $fileIdentifier)) {
            return true;
        }
        $file = $this->getFile($fileIdentifier);
        if ($file !== null) {
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
        if ($folder === null) {
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
        return $this->getFolder($folderIdentifier) !== null;
    }

    public function createFolder(string $folderIdentifier): void
    {
        if (!$this->folderExists($folderIdentifier)) {
            $identifierParts = explode(':', $folderIdentifier);
            $storageUid = array_shift($identifierParts);
            $path = implode(':', $identifierParts);
            $storage = $this->resourceFactory->getStorageObject($storageUid);
            try {
                $storage->createFolder($path);
            } catch (Exception $e) {
                throw new DigitalMarketingFrameworkException($e->getMessage(), $e->getCode(), $e);
            }
        }
    }
}
