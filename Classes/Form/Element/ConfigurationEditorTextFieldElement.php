<?php

namespace DigitalMarketingFramework\Typo3\Core\Form\Element;

use DigitalMarketingFramework\Core\ConfigurationEditor\MetaData;
use DigitalMarketingFramework\Typo3\Core\Registry\RegistryCollection;
use DigitalMarketingFramework\Typo3\Core\Utility\ConfigurationEditorRenderUtility;
use DOMDocument;
use DOMElement;
use TYPO3\CMS\Backend\Form\Element\TextElement;
use TYPO3\CMS\Core\Page\JavaScriptModuleInstruction;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ConfigurationEditorTextFieldElement extends TextElement
{
    /**
     * @var string
     */
    public const RENDER_TYPE = 'digitalMarketingFrameworkConfigurationEditorTextFieldElement';

    protected function getContextIdentifier(): string
    {
        // TODO add a (typo3-specific?) entry point for different context identifier producers
        $tableName = $this->data['tableName'] ?? '';
        $cType = $this->data['databaseRow']['CType'][0] ?? '';
        if ($tableName === 'tt_content' && $cType === 'form_formframework') {
            return 'form:' . $this->data['databaseRow']['pi_flexform']['data']['sDEF']['lDEF']['settings.persistenceIdentifier']['vDEF'][0] ?? '';
        }

        if ($tableName === 'tx_dmfcore_domain_model_api_endpoint') {
            return 'api:' . $this->data['databaseRow']['name'];
        }

        return '';
    }

    /**
     * @param array{
     *   readOnly?:bool,
     *   mode?:string,
     *   globalDocument?:bool,
     *   contextIdentifier?:string,
     *   ajaxControllerDocumentType?:string,
     *   ajaxControllerSupportsIncludes?:bool,
     *   ajaxControllerAdditionalParameters?:array<string,string>
     * } $config
     */
    protected function updateTextArea(DOMElement $textArea, array $config): void
    {
        $readonly = $config['readOnly'] ?? false;
        $mode = $config['mode'] ?? 'modal';
        $globalDocument = $config['globalDocument'] ?? false;
        $contextIdentifier = $config['contextIdentifier'] ?? $this->getContextIdentifier();
        $documentType = $config['ajaxControllerDocumentType'] ?? MetaData::DEFAULT_DOCUMENT_TYPE;
        $includes = $config['ajaxControllerSupportsIncludes'] ?? true;
        $parameters = $config['ajaxControllerAdditionalParameters'] ?? [];

        $dataAttributes = ConfigurationEditorRenderUtility::getTextAreaDataAttributes(
            ready: true,
            mode: $mode,
            readonly: $readonly,
            globalDocument: $globalDocument,
            documentType: $documentType,
            includes: $includes,
            parameters: $parameters,
            contextIdentifier: $contextIdentifier,
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

    /**
     * @return array<string,mixed>
     */
    public function render(): array
    {
        $resultArray = parent::render();

        $registryCollection = GeneralUtility::makeInstance(RegistryCollection::class);
        $assetService = $registryCollection->getRegistry()->getAssetService();

        $parameterArray = $this->data['parameterArray'];
        $config = $parameterArray['fieldConf']['config'];
        foreach (MetaData::SCRIPTS as $path) {
            $script = $assetService->makeAssetPublic($path);
            $resultArray['javaScriptModules'][] = JavaScriptModuleInstruction::create('/' . $script);
        }

        foreach (MetaData::STYLES as $path) {
            $resultArray['stylesheetFiles'][] = '/' . $assetService->makeAssetPublic($path);
        }

        foreach (MetaData::ASSETS as $path) {
            $assetService->makeAssetPublic($path);
        }

        $doc = new DOMDocument();
        $doc->loadHTML($resultArray['html'], LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOWARNING | LIBXML_NOERROR);

        $textAreas = $doc->getElementsByTagName('textarea');
        if ($textAreas->length === 1) {
            /** @var DOMElement $textArea */
            $textArea = $textAreas->item(0);
            $this->updateTextArea($textArea, $config);
        }

        $resultArray['html'] = $doc->saveHTML();

        return $resultArray;
    }
}
