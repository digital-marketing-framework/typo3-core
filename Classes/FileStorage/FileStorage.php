<?php

namespace DigitalMarketingFramework\Typo3\Core\FileStorage;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\FileStorage\AccessProtectionTrait;
use DigitalMarketingFramework\Core\FileStorage\FileStorageInterface;
use DigitalMarketingFramework\Core\Log\LoggerAwareInterface;
use DigitalMarketingFramework\Core\Log\LoggerAwareTrait;
use DigitalMarketingFramework\Core\Model\Data\Value\FileValue;
use DigitalMarketingFramework\Core\Model\Data\Value\FileValueInterface;
use DigitalMarketingFramework\Core\Utility\WebServerUtility;
use Exception;
use InvalidArgumentException;
use Throwable;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Resource\Exception\ResourceDoesNotExistException;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\Resource\StorageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FileStorage implements FileStorageInterface, LoggerAwareInterface
{
    use AccessProtectionTrait;

    use LoggerAwareTrait;

    protected ?int $defaultStorageUid = null;

    public function __construct(
        protected ResourceFactory $resourceFactory,
        protected StorageRepository $storageRepository,
    ) {
    }

    protected function getDefaultStorageUid(): int
    {
        if ($this->defaultStorageUid === null) {
            // ResourceFactory::getDefaultStorage() and ::getStorageObject() were removed
            // in TYPO3 14 (#107735) — use StorageRepository. Available identically in v12+.
            $defaultStorage = $this->storageRepository->getDefaultStorage();
            if (!$defaultStorage instanceof ResourceStorage) {
                throw new DigitalMarketingFrameworkException('No default resource storage found', 5349599510);
            }

            $this->defaultStorageUid = $defaultStorage->getUid();
        }

        return $this->defaultStorageUid;
    }

    protected function sanitizeIdentifier(string $identifier): string
    {
        $identifierParts = explode(':', $identifier);
        if (count($identifierParts) === 1) {
            return $this->getDefaultStorageUid() . ':' . $identifier;
        }

        return $identifier;
    }

    protected function getResource(string $identifier): File|Folder|null
    {
        $identifier = $this->sanitizeIdentifier($identifier);
        try {
            return $this->resourceFactory->retrieveFileOrFolderObject($identifier);
        } catch (ResourceDoesNotExistException) {
            return null;
        } catch (InvalidArgumentException) {
            // StorageRepository throws this when the identifier names a storage uid that has no
            // record. An identifier pointing at a storage that is not there describes a resource
            // that is not there, and every caller here already handles that.
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

            // Writing a file implies the folder it goes in: nobody should have to create the
            // storage folder by hand before the first document can be saved. createFolder()
            // makes only what is missing and protects the folder either way, which is how a
            // folder that was there all along gets its access file.
            $folderIdentifier = $storageUid . ':' . $pathinfo['dirname'];
            $this->createFolder($folderIdentifier);

            $folder = $this->getFolder($folderIdentifier);
            if (!$folder instanceof Folder) {
                throw new DigitalMarketingFrameworkException(sprintf('Folder "%s" could not be created', $folderIdentifier), 1757462100);
            }

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
            throw new DigitalMarketingFrameworkException(sprintf('File "%s" not found', $fileIdentifier), 5280807608);
        }

        $folder = $this->getFolder($folderIdentifier);
        if (!$folder instanceof Folder) {
            throw new DigitalMarketingFrameworkException(sprintf('Folder "%s" not found', $folderIdentifier), 5791461439);
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
            $storage = $this->getStorage($storageUid);
            if (!$storage instanceof ResourceStorage) {
                throw new DigitalMarketingFrameworkException(sprintf('No file storage with uid "%d"', $storageUid), 1757462200);
            }

            try {
                $storage->createFolder($path);
            } catch (Exception $e) {
                throw new DigitalMarketingFrameworkException($e->getMessage(), $e->getCode(), $e);
            }
        }

        $this->protectFolder($folderIdentifier);
    }

    /**
     * The storage a uid names, or null when there is none.
     *
     * StorageRepository throws for an unknown uid, and every caller here is better served by an
     * answer than by an exception escaping into a bootstrap.
     */
    protected function getStorage(int $storageUid): ?ResourceStorage
    {
        try {
            return $this->storageRepository->getStorageObject($storageUid);
        } catch (Throwable) {
            return null;
        }
    }

    protected function getStorageForIdentifier(string $identifier): ?ResourceStorage
    {
        $identifierParts = explode(':', $this->sanitizeIdentifier($identifier));

        return $this->getStorage((int)array_shift($identifierParts));
    }

    public function folderIsWriteable(string $folderIdentifier): bool
    {
        $storage = $this->getStorageForIdentifier($folderIdentifier);

        // The storage record has to exist, be switched on and allow writing. The folder itself
        // need not be there — it is created with the first file.
        return $storage instanceof ResourceStorage
            && $storage->isOnline()
            && $storage->isWritable();
    }

    public function isPubliclyAccessible(string $identifier): bool
    {
        return $this->getStorageForIdentifier($identifier)?->isPublic() ?? false;
    }

    public function protectFolder(string $folderIdentifier): void
    {
        $folderIdentifier = $this->sanitizeIdentifier($folderIdentifier);
        if (!WebServerUtility::supportsAccessFile()) {
            return;
        }

        $storage = $this->getStorageForIdentifier($folderIdentifier);
        if (!$storage instanceof ResourceStorage || !$storage->isPublic()) {
            return;
        }

        // Not through FAL: "^\.htaccess$" is part of TYPO3's default fileDenyPattern, so a
        // deny file can never be created that way. Only a local driver has a path to write to,
        // and only a local driver is served by a web server that reads one.
        if ($storage->getDriverType() !== 'Local') {
            return;
        }

        $folder = $this->getFolder($folderIdentifier);
        if (!$folder instanceof Folder) {
            return;
        }

        $basePath = (string)($storage->getConfiguration()['basePath'] ?? '');
        if ($basePath === '') {
            return;
        }

        if (($storage->getConfiguration()['pathType'] ?? 'relative') === 'relative') {
            $basePath = Environment::getPublicPath() . '/' . $basePath;
        }

        $accessFilePath = rtrim($basePath, '/') . '/' . trim($folder->getIdentifier(), '/') . '/' . static::ACCESS_FILE_NAME;
        // A folder that takes no new file cannot be protected from here. Saying so through a
        // PHP warning on every attempt is not saying it to anyone who can act on it; the
        // storage answers isStorageReady() with false, which is what reaches the backend.
        $accessFileFolder = dirname($accessFilePath);
        if (file_exists($accessFilePath) || !is_dir($accessFileFolder) || !is_writable($accessFileFolder)) {
            return;
        }

        file_put_contents($accessFilePath, static::ACCESS_FILE_CONTENTS);
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
