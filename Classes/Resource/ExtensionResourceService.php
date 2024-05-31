<?php

namespace DigitalMarketingFramework\Typo3\Core\Resource;

use DigitalMarketingFramework\Core\Resource\ResourceService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;

class ExtensionResourceService extends ResourceService
{
    public function getIdentifierPrefix(): string
    {
        return 'EXT';
    }

    public function getResourcePath(string $identifier): ?string
    {
        if (!$this->resourceIdentifierMatch($identifier)) {
            return null;
        }

        return GeneralUtility::getFileAbsFileName($identifier);
    }

    public function resourceIdentifierMatch(string $identifier): bool
    {
        if (!PathUtility::isExtensionPath($identifier)) {
            return false;
        }

        return preg_match('/^EXT:[_a-z0-9]+\\/Resources/', $identifier);
    }

    public function getFileIdentifiersInResourceFolder(string $folderIdentifier): array|false
    {
        return $this->getResourcePath($folderIdentifier);
    }

    public function getResourceRootIdentifier(string $identifier): ?string
    {
        if (!$this->resourceIdentifierMatch($identifier)) {
            return null;
        }

        $matches = [];
        $result = preg_match('/^(EXT:[_a-z0-9]+\\/Resources)/', $identifier, $matches);

        if ($result) {
            return $matches[1];
        }

        return null;
    }

    public function isAssetResource(string $identifier): bool
    {
        return $this->isResourceInFolder($identifier, 'Public');
    }

    public function isPublicResource(string $identifier): bool
    {
        return $this->isAssetResource($identifier);
    }
}
