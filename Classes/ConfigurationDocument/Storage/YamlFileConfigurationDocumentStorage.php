<?php

namespace DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Storage;

use DigitalMarketingFramework\Core\ConfigurationDocument\Storage\YamlFileConfigurationDocumentStorage as OriginalYamlFileConfigurationDocumentStorage;
use Exception;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class YamlFileConfigurationDocumentStorage extends OriginalYamlFileConfigurationDocumentStorage
{
    /**
     * @var string
     */
    protected const ACCESS_FILE_PATH = 'EXT:dmf_core/Resources/Private/StaticTemplates/.htaccess';

    public function initializeConfigurationDocumentStorage(): void
    {
        parent::initializeConfigurationDocumentStorage();
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
