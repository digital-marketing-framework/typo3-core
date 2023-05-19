<?php

namespace DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Storage;

use TYPO3\CMS\Core\Resource\File;

class JsonFileConfigurationDocumentStorage extends FileConfigurationDocumentStorage
{
    protected function getFileExtension(): string
    {
        return 'json';
    }

    protected function checkFileValidity(string $fileIdentifier): bool
    {
        return parent::checkFileValidity($fileIdentifier)
            && strtolower($this->fileStorage->getFileExtension($fileIdentifier)) === 'json';
    }
}
