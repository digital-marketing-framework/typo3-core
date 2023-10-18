<?php

namespace DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Storage\EventListener;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;

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
                if ($file === '.' || $file === '..' || strtolower($file) === '.gitkeep') {
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
        if (PathUtility::isExtensionPath($documentIdentifier)) {
            $absoluteFilePath = GeneralUtility::getFileAbsFileName($documentIdentifier);
            if ($absoluteFilePath !== '') {
                return file_get_contents($absoluteFilePath);
            }
        }

        return null;
    }
}
