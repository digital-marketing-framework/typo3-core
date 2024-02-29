<?php

namespace DigitalMarketingFramework\Typo3\Core\FileStorage;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\FileStorage\FileStorageInterface;
use DigitalMarketingFramework\Core\Log\LoggerAwareInterface;
use DigitalMarketingFramework\Core\Log\LoggerAwareTrait;
use DigitalMarketingFramework\Core\Model\Data\Value\FileValue;
use DigitalMarketingFramework\Core\Model\Data\Value\FileValueInterface;
use Exception;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Resource\Exception\ResourceDoesNotExistException;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FileStorage implements FileStorageInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected ?int $defaultStorageUid = null;

    public function __construct(
        protected ResourceFactory $resourceFactory,
    ) {
    }

    protected function getDefaultStorageUid(): int
    {
        if ($this->defaultStorageUid === null) {
            $defaultStorage = $this->resourceFactory->getDefaultStorage();
            if (!$defaultStorage instanceof ResourceStorage) {
                throw new DigitalMarketingFrameworkException('No default resource storage found');
            }

            $this->defaultStorageUid = $defaultStorage->getUid();
        }

        return $this->defaultStorageUid;
    }

    protected function sanitizeIdentifier(string $identifier): string
    {
        $identifierParts = explode(':', $identifier);
        if (count($identifierParts) === 1) {
            $identifier = $this->getDefaultStorageUid() . ':' . $identifier;
        }

        return $identifier;
    }

    protected function getResource(string $identifier): File|Folder|null
    {
        $identifier = $this->sanitizeIdentifier($identifier);
        try {
            return $this->resourceFactory->retrieveFileOrFolderObject($identifier);
        } catch (ResourceDoesNotExistException $e) {
            $this->logger->error(sprintf('Resource with ID "%s" does not exist: %s', $identifier, $e->getMessage()));

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
        $folder = $this->getResource($identifier);
        if ($folder instanceof Folder) {
            return $folder;
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
        $fileIdentifier = $this->sanitizeIdentifier($fileIdentifier);
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
        return $this->getFile($fileIdentifier) instanceof File;
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

    public function copyFileToFolder(string $fileIdentifier, string $folderIdentifier): string
    {
        $file = $this->getFile($fileIdentifier);
        if (!$file instanceof File) {
            throw new DigitalMarketingFrameworkException(sprintf('File "%s" not found', $fileIdentifier));
        }

        $folder = $this->getFolder($folderIdentifier);
        if (!$folder instanceof Folder) {
            throw new DigitalMarketingFrameworkException(sprintf('Folder "%s" not found', $folderIdentifier));
        }

        $copiedFile = $file->copyTo($folder);

        return $copiedFile->getCombinedIdentifier();
    }

    public function createFolder(string $folderIdentifier): void
    {
        $folderIdentifier = $this->sanitizeIdentifier($folderIdentifier);
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

    protected function getOrigin(): string
    {
        return GeneralUtility::getIndpEnv('TYPO3_SITE_URL');
    }

    protected function getResourcePublicUrl(File|Folder|null $resource): string
    {
        if ($resource === null) {
            return '';
        }

        return rtrim($this->getOrigin(), '/')
            . '/'
            . ltrim($resource->getPublicUrl() ?? '', '/');
    }

    public function getPublicUrl(string $fileIdentifier): string
    {
        $resource = $this->getResource($fileIdentifier);

        return $this->getResourcePublicUrl($resource);
    }

    public function getMimeType(string $fileIdentifier): string
    {
        return $this->getResource($fileIdentifier)?->getMimeType() ?? '';
    }

    public function getFileValue(string $fileIdentifier): ?FileValueInterface
    {
        $file = $this->getFile($fileIdentifier);
        if (!$file instanceof File) {
            return null;
        }

        return new FileValue(
            $fileIdentifier,
            $file->getName(),
            $this->getResourcePublicUrl($file),
            $file->getMimeType()
        );
    }

    public function getTempPath(): string
    {
        return Environment::getVarPath() . '/transient/';
    }

    public function writeTempFile(string $filePrefix = '', string $fileContent = '', string $fileSuffix = ''): string|false
    {
        $result = null;
        $filePath = GeneralUtility::tempnam($filePrefix, $fileSuffix);
        if (is_writable($filePath)) {
            $result = file_put_contents($filePath, $fileContent);
        } else {
            $this->logger->warning(sprintf('File %s does not seem to be writeable.', $filePath));
        }

        return $result ? $filePath : false;
    }
}
