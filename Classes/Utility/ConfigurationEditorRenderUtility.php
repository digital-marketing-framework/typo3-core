<?php

namespace DigitalMarketingFramework\Typo3\Core\Utility;

use DigitalMarketingFramework\Core\ConfigurationEditor\MetaData;
use DigitalMarketingFramework\Typo3\Core\Backend\UriBuilder;
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
     * @param array<string,string> $parameters
     *
     * @return array<string,string>
     */
    public static function getTextAreaDataAttributes(
        bool $ready,
        string $mode,
        bool $readonly,
        bool $globalDocument,
        string $documentType = MetaData::DEFAULT_DOCUMENT_TYPE,
        bool $includes = true,
        array $parameters = [],
        string $contextIdentifier = '',
    ): array {
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);

        $extSettings = static::getExtensionConfiguration();
        $debug = $extSettings['debug'] ?? false;

        $parameters['documentType'] = $documentType;
        $dataAttributes = [
            'app' => $ready ? 'true' : 'false',
            'mode' => $mode,
            'readonly' => $readonly ? 'true' : 'false',
            'global-document' => $globalDocument ? 'true' : 'false',
            'debug' => $debug ? 'true' : 'false',
            'context-identifier' => $contextIdentifier,

            'url-schema' => $uriBuilder->build('ajax.configuration-editor.schema', $parameters),
            'url-defaults' => $uriBuilder->build('ajax.configuration-editor.defaults', $parameters),
            'url-merge' => $uriBuilder->build('ajax.configuration-editor.merge', $parameters),
            'url-split' => $uriBuilder->build('ajax.configuration-editor.split', $parameters),
        ];

        if ($includes) {
            $dataAttributes['url-update-includes'] = $uriBuilder->build('ajax.configuration-editor.update-includes', $parameters);
        }

        return $dataAttributes;
    }
}
