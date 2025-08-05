<?php

namespace DigitalMarketingFramework\Typo3\Core\Form\Element;

use DigitalMarketingFramework\Core\Backend\RenderingServiceInterface;
use DigitalMarketingFramework\Core\ConfigurationEditor\MetaData;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Core\Resource\Asset\AssetServiceInterface;
use DigitalMarketingFramework\Typo3\Core\Registry\RegistryCollection;
use DOMDocument;
use DOMElement;
use TYPO3\CMS\Backend\Form\Element\TextElement;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Page\JavaScriptModuleInstruction;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ConfigurationEditorTextFieldElement extends TextElement
{
    /**
     * @var string
     */
    public const RENDER_TYPE = 'digitalMarketingFrameworkConfigurationEditorTextFieldElement';

    /**
     * @var string
     */
    public const JS_VENDOR = '@digital-marketing-framework';

    protected RegistryInterface $registry;

    protected AssetServiceInterface $assetService;

    protected RenderingServiceInterface $renderingService;

    protected function getRegistry(): RegistryInterface
    {
        if (!isset($this->registry)) {
            $registryCollection = GeneralUtility::makeInstance(RegistryCollection::class);
            $this->registry = $registryCollection->getRegistry();
        }

        return $this->registry;
    }

    protected function getAssetService(): AssetServiceInterface
    {
        if (!isset($this->assetService)) {
            $this->assetService = $this->getRegistry()->getAssetService();
        }

        return $this->assetService;
    }

    protected function getBackendRenderingService(): RenderingServiceInterface
    {
        if (!isset($this->renderingService)) {
            $this->renderingService = $this->getRegistry()->getBackendRenderingService();
        }

        return $this->renderingService;
    }

    protected function getContextIdentifier(): string
    {
        // TODO add a (typo3-specific?) entry point for different context identifier producers
        $tableName = $this->data['tableName'] ?? '';
        $cType = $this->data['databaseRow']['CType'][0] ?? '';
        if ($tableName === 'tt_content' && $cType === 'form_formframework') {
            $id = $this->data['databaseRow']['pi_flexform']['data']['sDEF']['lDEF']['settings.persistenceIdentifier']['vDEF'][0] ?? '';
            if ($id !== '') {
                return 'form:' . $id;
            }
        }

        if ($tableName === 'tx_dmfcore_domain_model_api_endpoint') {
            $name = $this->data['databaseRow']['name'] ?? '';
            if ($name !== '') {
                return 'api:' . $name;
            }
        }

        return '';
    }

    protected function getUid(): string
    {
        // TODO add a (typo3-specific?) entry point for different uid producers
        $tableName = $this->data['tableName'] ?? '';
        $cType = $this->data['databaseRow']['CType'][0] ?? '';
        if ($tableName === 'tt_content' && $cType === 'form_formframework') {
            $id = $this->data['databaseRow']['pi_flexform']['data']['sDEF']['lDEF']['settings.persistenceIdentifier']['vDEF'][0] ?? '';
            $uid = $this->data['databaseRow']['uid'] ?? 0;
            if ($id !== '' && $uid !== 0) {
                $workspaceId = $this->data['databaseRow']['t3ver_wsid'] ?? 0;
                $languageId = $this->data['databaseRow']['sys_language_uid'] ?? 0;

                return sprintf('form-plugin-%d-%d-%d:%s', $uid, $workspaceId, $languageId, $id);
            }
        }

        if ($tableName === 'tx_dmfcore_domain_model_api_endpoint') {
            $name = $this->data['databaseRow']['name'] ?? '';
            if ($name !== '') {
                return 'api:' . $name;
            }
        }

        return '';
    }

    /**
     * @param array{
     *   readOnly?:bool,
     *   mode?:string,
     *   globalDocument?:bool,
     *   contextIdentifier?:string,
     *   uid?:string,
     *   ajaxControllerDocumentType?:string,
     *   ajaxControllerSupportsIncludes?:bool,
     *   ajaxControllerAdditionalParameters?:array<string,string>
     * } $config
     */
    protected function updateTextArea(DOMElement $textArea, array $config): void
    {
        $dataAttributes = $this->getBackendRenderingService()->getTextAreaDataAttributes(
            ready: true,
            mode: $config['mode'] ?? 'modal',
            readonly: $config['readOnly'] ?? false,
            globalDocument: $config['globalDocument'] ?? false,
            documentType: $config['ajaxControllerDocumentType'] ?? MetaData::DEFAULT_DOCUMENT_TYPE,
            includes: $config['ajaxControllerSupportsIncludes'] ?? true,
            parameters: $config['ajaxControllerAdditionalParameters'] ?? [],
            contextIdentifier: $config['contextIdentifier'] ?? $this->getContextIdentifier(),
            uid: $config['uid'] ?? $this->getUid()
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
        $assetService = $this->getAssetService();

        $parameterArray = $this->data['parameterArray'];
        $config = $parameterArray['fieldConf']['config'];
        $typo3Version = new Typo3Version();
        foreach (MetaData::SCRIPTS as $path) {
            $instructionName = static::JS_VENDOR . '/' . $path;
            if ($typo3Version->getMajorVersion() <= 12) {
                $instructionName = '/' . $assetService->makeAssetPublic($path);
            }

            $resultArray['javaScriptModules'][] = JavaScriptModuleInstruction::create($instructionName);
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
