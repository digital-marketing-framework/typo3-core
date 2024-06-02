<?php

namespace DigitalMarketingFramework\Typo3\Core\ExpressionLanguage;

use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class TypoScriptDigitalMarketingFramework
{
    public function extensionLoaded(string $extKey): bool
    {
        return ExtensionManagementUtility::isLoaded($extKey);
    }

    /**
     * @return array<string,mixed>
     */
    protected function getCoreConfiguration(): array
    {
        $extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class);
        try {
            return $extensionConfiguration->get('dmf_core');
        } catch (ExtensionConfigurationExtensionNotConfiguredException|ExtensionConfigurationPathDoesNotExistException) {
            return [];
        }
    }

    public function apiEnabled(): bool
    {
        return $this->getCoreConfiguration()['api']['enabled'] ?? false;
    }
}
