<?php

namespace DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Storage;

use TYPO3\CMS\Core\Resource\File;

class YamlFileConfigurationDocumentStorage extends FileConfigurationDocumentStorage
{
    protected function checkFileValidity(File $file): bool
    {
        return parent::checkFileValidity($file)
            && in_array(strtolower($file->getExtension()), ['yml', 'yaml']);
    }
}
