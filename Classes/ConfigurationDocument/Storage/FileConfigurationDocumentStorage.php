<?php

namespace DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Storage;

use DigitalMarketingFramework\Core\ConfigurationDocument\Storage\FileConfigurationDocumentStorage as OriginalFileConfigurationDocumentStorage;
use Exception;

abstract class FileConfigurationDocumentStorage extends OriginalFileConfigurationDocumentStorage
{
    protected const ACCESS_FILE_PATH = 'EXT:digitalmarketingframework/Resources/Private/StaticTemplates/.htaccess';

    public function initalizeConfigurationDocumentStorage(): void
    {
        parent::initalizeConfigurationDocumentStorage();
        $accessFileIdentifier = $this->getStorageFolderIdentifier() . '/' . '.htaccess';
        if (!$this->fileStorage->fileExists($accessFileIdentifier)) {
            try {
                $accessFileContents = $this->fileStorage->getFileContents(static::ACCESS_FILE_PATH);
                $this->fileStorage->putFileContents($accessFileIdentifier, $accessFileContents);
            } catch (Exception) {
                $this->logger->warning(sprintf('Unable to create .htaccess file "%s"', $accessFileIdentifier));
            }
        }
    }
}
