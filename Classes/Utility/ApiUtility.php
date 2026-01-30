<?php

namespace DigitalMarketingFramework\Typo3\Core\Utility;

use DigitalMarketingFramework\Core\GlobalConfiguration\Schema\CoreGlobalConfigurationSchema;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Lightweight utility for reading API configuration without bootstrapping Anyrel.
 *
 * This class provides static methods to check API status and configuration
 * without triggering the full Anyrel bootstrap process. This is critical for
 * performance when generating routes or checking API availability.
 */
class ApiUtility
{
    /**
     * Get the extension settings from TYPO3's extension configuration.
     *
     * @return ?array<string,mixed>
     *   The extension configuration array, or NULL if not available
     */
    protected static function getExtensionSettings(): ?array
    {
        try {
            $extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class);

            return $extensionConfiguration->get('dmf_core');
        } catch (ExtensionConfigurationExtensionNotConfiguredException|ExtensionConfigurationPathDoesNotExistException) {
            return null;
        }
    }

    /**
     * Check if the Anyrel API is enabled.
     *
     * Reads directly from TYPO3's extension configuration without bootstrapping Anyrel.
     *
     * @return bool
     *   TRUE if API is enabled, FALSE otherwise
     */
    public static function enabled(): bool
    {
        $config = static::getExtensionSettings();

        return (bool)($config[CoreGlobalConfigurationSchema::KEY_API][CoreGlobalConfigurationSchema::KEY_API_ENABLED] ?? CoreGlobalConfigurationSchema::DEFAULT_API_ENABLED);
    }

    /**
     * Get the configured API base path.
     *
     * Reads directly from TYPO3's extension configuration without bootstrapping Anyrel.
     *
     * @return string
     *   The configured base path (e.g., 'digital-marketing-framework/api').
     */
    public static function getBasePath(): string
    {
        $config = static::getExtensionSettings();

        return (string)($config[CoreGlobalConfigurationSchema::KEY_API][CoreGlobalConfigurationSchema::KEY_API_BASE_PATH] ?? CoreGlobalConfigurationSchema::DEFAULT_API_BASE_PATH);
    }
}
