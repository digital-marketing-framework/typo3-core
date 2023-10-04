<?php

namespace DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Storage\EventListener;

use TYPO3\CMS\Core\Utility\GeneralUtility;

abstract class StaticConfigurationDocumentEventListener extends AbstractStaticConfigurationDocumentEventListener
{
    /**
     * @var string
     */
    protected const PATH_PATTERN = 'EXT:%s/Resources/Private/ConfigurationDocuments';

    abstract protected function getExtensionKey(): string;

    protected function getFolderPath(): string
    {
        $path = sprintf(static::PATH_PATTERN, $this->getExtensionKey());

        return GeneralUtility::getFileAbsFileName($path);
    }

    protected function getIdentifiers(): array
    {
        $results = [];
        $path = $this->getFolderPath();
        if (is_dir($path)) {
            $files = scandir($path);
            if ($files === false) {
                $files = [];
            }

            foreach ($files as $file) {
                if ($file === '.' || $file === '..') {
                    continue;
                }

                $filePath = sprintf('%s/%s', $path, $file);
                if (!is_file($filePath)) {
                    continue;
                }

                $results[] = sprintf(static::PATH_PATTERN . '/%s', $this->getExtensionKey(), $file);
            }
        }

        return $results;
    }

    protected function getDocument(string $documentIdentifier, bool $metaDataOnly = false): ?string
    {
        // NOTE: the normal identifiers can be loaded without the need for custom development
        // example: EXT:ext_key/Resources/Private/ConfigurationDocuments/custom_name.config.yaml
        return null;
    }
}
