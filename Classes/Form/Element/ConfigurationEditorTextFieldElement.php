<?php

namespace DigitalMarketingFramework\Typo3\Core\Form\Element;

use DigitalMarketingFramework\Core\Utility\GeneralUtility as DmfGeneralUtility;
use DigitalMarketingFramework\Typo3\Core\Registry\RegistryCollection;
use DigitalMarketingFramework\Typo3\Core\Utility\ConfigurationEditorRenderUtility;
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
     * @param array{
     *   readOnly?:bool,
     *   mode?:string,
     *   globalDocument?:bool,
     *   ajaxControllerBaseRoute?:string,
     *   ajaxControllerSupportsIncludes?:bool,
     *   ajaxControllerAdditionalParameters?:array<string,string>
     * } $config
     */
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

    /**
     * @param array{ajaxControllerSupportsIncludes?:bool} $config
     */
    protected function controllerSupportsIncludes(array $config): bool
    {
        return $config['ajaxControllerSupportsIncludes'] ?? true;
    }

    /**
     * @param array{ajaxControllerBaseRoute?:string} $config
     */
    protected function getControllerBaseRoute(array $config): string
    {
        return $config['ajaxControllerBaseRoute'] ?? 'configuration';
    }

    /**
     * @param array{ajaxControllerAdditionalParameters?:array<string,string>} $config
     *
     * @return array<string,string>
     */
    protected function getAdditionalControllerParameters(array $config): array
    {
        return $config['ajaxControllerAdditionalParameters'] ?? [];
    }

    protected function createJavaScriptModuleInstruction(string $name): JavaScriptModuleInstruction
    {
        $typo3Version = new Typo3Version();
        if ($typo3Version->getMajorVersion() <= 11) {
            return JavaScriptModuleInstruction::forRequireJS($name); // @phpstan-ignore-line TYPO3 version switch
        }

        return JavaScriptModuleInstruction::create($name); // @phpstan-ignore-line TYPO3 version switch
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

        $scriptUrl = $assetService->makeAssetPublic('PKG:digital-marketing-framework/core/res/assets/config-editor/scripts/index.js');
        $stylesUrl = $assetService->makeAssetPublic('PKG:digital-marketing-framework/core/res/assets/config-editor/styles/index.css');
        $fontStylesUrl = $assetService->makeAssetPublic('PKG:digital-marketing-framework/core/res/assets/config-editor/styles/type.css');
        $assetService->makeAssetPublic('PKG:digital-marketing-framework/core/res/assets/config-editor/fonts/caveat/Caveat-Bold.ttf');
        $assetService->makeAssetPublic('PKG:digital-marketing-framework/core/res/assets/config-editor/fonts/caveat/Caveat-Medium.ttf');
        $assetService->makeAssetPublic('PKG:digital-marketing-framework/core/res/assets/config-editor/fonts/caveat/Caveat-Regular.ttf');
        $assetService->makeAssetPublic('PKG:digital-marketing-framework/core/res/assets/config-editor/fonts/caveat/Caveat-SemiBold.ttf');

        $doc = new DOMDocument();
        $doc->loadHTML($resultArray['html'], LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        $typo3Version = new Typo3Version();
        if ($typo3Version->getMajorVersion() <= 11) {
            $javaScriptModulesField = 'requireJsModules';
            $javaScriptModuleInstruction = JavaScriptModuleInstruction::forRequireJS('/' . $scriptUrl); // @phpstan-ignore-line TYPO3 version switch
        } else {
            $javaScriptModulesField = 'javaScriptModules';
            $javaScriptModuleInstruction = JavaScriptModuleInstruction::create('/' . $scriptUrl); // @phpstan-ignore-line TYPO3 version switch
        }

        $textAreas = $doc->getElementsByTagName('textarea');
        if ($textAreas->length === 1) {
            /** @var DOMElement $textArea */
            $textArea = $textAreas->item(0);
            $this->updateTextArea($textArea, $config);
        }

        $resultArray['stylesheetFiles'][] = '/' . $stylesUrl;
        $resultArray['stylesheetFiles'][] = '/' . $fontStylesUrl;
        $resultArray[$javaScriptModulesField][] = $javaScriptModuleInstruction;
        $resultArray['html'] = $doc->saveHTML();

        return $resultArray;
    }
}
