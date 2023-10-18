<?php

namespace DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Storage;

use DigitalMarketingFramework\Core\ConfigurationDocument\Storage\FileConfigurationDocumentStorage as OriginalFileConfigurationDocumentStorage;
use Exception;
use TYPO3\CMS\Core\Utility\GeneralUtility;

abstract class FileConfigurationDocumentStorage extends OriginalFileConfigurationDocumentStorage
{
    /**
     * @var string
     */
    protected const ACCESS_FILE_PATH = 'EXT:dmf_core/Resources/Private/StaticTemplates/.htaccess';

    public function initalizeConfigurationDocumentStorage(): void
    {
        parent::initalizeConfigurationDocumentStorage();
        $accessFileIdentifier = $this->getStorageFolderIdentifier() . '/.htaccess';
        if (!$this->fileStorage->fileExists($accessFileIdentifier)) {
            try {
                $accessFileSourcePath = GeneralUtility::getFileAbsFileName(static::ACCESS_FILE_PATH);
                $accessFileContents = file_get_contents($accessFileSourcePath);
                $this->fileStorage->putFileContents($accessFileIdentifier, $accessFileContents);
            } catch (Exception) {
                $this->logger->warning(sprintf('Unable to create .htaccess file "%s"', $accessFileIdentifier));
            }
        }
    }
}
