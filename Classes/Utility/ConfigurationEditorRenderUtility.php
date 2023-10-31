<?php

namespace DigitalMarketingFramework\Typo3\Core\Utility;

use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ConfigurationEditorRenderUtility
{
    /**
     * @return array{debug?:bool}
     */
    protected static function getExtensionConfiguration(): array
    {
        $extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class);
        try {
            return $extensionConfiguration->get('dmf_core');
        } catch (ExtensionConfigurationExtensionNotConfiguredException|ExtensionConfigurationPathDoesNotExistException) {
            return [];
        }
    }

    /**
     * @return array<string,string>
     */
    public static function getTextAreaDataAttributes(bool $ready, string $mode, bool $readonly, bool $globalDocument): array
    {
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);

        $extSettings = static::getExtensionConfiguration();
        $debug = $extSettings['debug'] ?? false;

        return [
            'app' => $ready ? 'true' : 'false',
            'mode' => $mode,
            'readonly' => $readonly ? 'true' : 'false',
            'global-document' => $globalDocument ? 'true' : 'false',
            'debug' => $debug ? 'true' : 'false',

            'url-schema' => (string)$uriBuilder->buildUriFromRoute('ajax_digitalmarketingframework_configuration_schema'),
            'url-defaults' => (string)$uriBuilder->buildUriFromRoute('ajax_digitalmarketingframework_configuration_defaults'),
            'url-merge' => (string)$uriBuilder->buildUriFromRoute('ajax_digitalmarketingframework_configuration_merge'),
            'url-split' => (string)$uriBuilder->buildUriFromRoute('ajax_digitalmarketingframework_configuration_split'),
            'url-update-includes' => (string)$uriBuilder->buildUriFromRoute('ajax_digitalmarketingframework_configuration_update_includes'),
        ];
    }
}
