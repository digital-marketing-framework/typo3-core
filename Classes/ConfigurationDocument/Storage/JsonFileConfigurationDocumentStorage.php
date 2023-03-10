<?php

namespace DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Storage;

use TYPO3\CMS\Core\Resource\File;

class JsonFileConfigurationDocumentStorage extends FileConfigurationDocumentStorage
{
    protected function checkFileValidity(File $file): bool
    {
        return parent::checkFileValidity($file)
            && strtolower($file->getExtension()) === 'json';
    }
}
