<?php

namespace DigitalMarketingFramework\Typo3\Core\Form\Element;

use DigitalMarketingFramework\Core\Utility\GeneralUtility;
use DigitalMarketingFramework\Typo3\Core\Utility\ConfigurationEditorRenderUtility;
use DigitalMarketingFramework\Typo3\Core\Utility\VendorAssetUtility;
use DOMDocument;
use DOMElement;
use TYPO3\CMS\Backend\Form\Element\TextElement;
use TYPO3\CMS\Core\Page\JavaScriptModuleInstruction;

class ConfigurationEditorTextFieldElement extends TextElement
{
    /**
     * @var string
     */
    public const RENDER_TYPE = 'digitalMarketingFrameworkConfigurationEditorTextFieldElement';

    protected function updateTextArea(DOMElement $textArea, array $config): void
    {
        $readonly = $config['readOnly'] ?? false;
        $mode = $config['mode'] ?? 'modal';
        $globalDocument = $config['globalDocument'] ?? false;
        $baseRoute = $this->getControllerBaseRoute($config);
        $includes = $this->controllerSupportsIncludes($config);
        $parameters = $this->getAdditionalControllerParameters($config);

        $dataAttributes = ConfigurationEditorRenderUtility::getTextAreaDataAttributes(
            ready: true,
            mode: $mode,
            readonly: $readonly,
            globalDocument: $globalDocument,
            baseRoute: $baseRoute,
            includes: $includes,
            parameters: $parameters,
        );

        $class = $textArea->getAttribute('class');

        if ($class !== '') {
            $class .= ' ';
        }

        $textArea->setAttribute('class', $class . 'dmf-configuration-document');

        foreach ($dataAttributes as $name => $value) {
            $textArea->setAttribute('data-' . $name, $value);
        }
    }

    protected function controllerSupportsIncludes(array $config): bool
    {
        return (bool)($config['ajaxControllerSupportsIncludes'] ?? true);
    }

    protected function getControllerBaseRoute(array $config): string
    {
        return $config['ajaxControllerBaseRoute'] ?? 'configuration';
    }

    protected function getAdditionalControllerParameters(array $config): array
    {
        $parameters = [];
        $fields = GeneralUtility::castValueToArray($config['additionalFlexFormSettingsAjaxControllerParameters'] ?? '');
        foreach ($fields as $field) {
            $value = $this->data['flexFormRowData']['settings.' . $field]['vDEF'][0] ?? '';
            if ($value !== '') {
                $parameters[$field] = $value;
            }
        }

        return $parameters;
    }

    public function render(): array
    {
        $resultArray = parent::render();

        $parameterArray = $this->data['parameterArray'];
        $config = $parameterArray['fieldConf']['config'];

        $scriptUrl = VendorAssetUtility::makeVendorAssetAvailable('digital-marketing-framework/core', '/config-editor/scripts/index.js');
        $stylesUrl = VendorAssetUtility::makeVendorAssetAvailable('digital-marketing-framework/core', '/config-editor/styles/index.css');
        $fontStylesUrl = VendorAssetUtility::makeVendorAssetAvailable(
            'digital-marketing-framework/core',
            '/config-editor/styles/type.css',
            [
                '/fonts/' => '/config-editor/fonts/',
            ]
        );

        $doc = new DOMDocument();
        $doc->loadHTML($resultArray['html'], LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $textAreas = $doc->getElementsByTagName('textarea');
        if ($textAreas->length === 1) {
            /** @var DOMElement $textArea */
            $textArea = $textAreas->item(0);
            $this->updateTextArea($textArea, $config);
        }

        $resultArray['stylesheetFiles'][] = '/' . $stylesUrl;
        $resultArray['stylesheetFiles'][] = '/' . $fontStylesUrl;
        $resultArray['javaScriptModules'][] = JavaScriptModuleInstruction::create('/' . $scriptUrl);
        $resultArray['html'] = $doc->saveHTML();

        return $resultArray;
    }
}
