<?php

namespace DigitalMarketingFramework\Typo3\Core\ViewHelpers\Be\ConfigurationEditor;

use DigitalMarketingFramework\Core\ConfigurationEditor\MetaData;
use DigitalMarketingFramework\Typo3\Core\Registry\RegistryCollection;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\ViewHelpers\Be\AbstractBackendViewHelper;

class TextAreaViewHelper extends AbstractBackendViewHelper
{
    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('mode', 'string', 'Editor mode', true);
        $this->registerArgument('ready', 'bool', 'Markup is initialized already', false, true);
        $this->registerArgument('readonly', 'bool', 'Read-only document', false, false);
        $this->registerArgument('globalDocument', 'bool', 'Global document', false, false);
        $this->registerArgument('documentType', 'string', 'Document type for ajax URLs', false, MetaData::DEFAULT_DOCUMENT_TYPE);
        $this->registerArgument('includes', 'bool', 'Configuration support includes', false, true);
        $this->registerArgument('routeParameters', 'array', 'Additional AJAX route parameters', false, []);
        $this->registerArgument('contextIdentifier', 'string', 'Context identifier', false, '');
        $this->registerArgument('uid', 'string', 'Unique editor identifier', false, '');
        $this->registerArgument('documentName', 'string', 'Document name for embedded documents', false, '');
        $this->registerArgument('contextType', 'string', 'Context type (form, form-plugin, api, etc.)', false, '');

        $this->registerArgument('additionalAttributes', 'array', 'Additional attributes', false, []);
    }

    public function render(): string
    {
        $attributes = GeneralUtility::makeInstance(RegistryCollection::class)->getRegistry()->getBackendRenderingService()->getTextAreaDataAttributes(
            ready: $this->arguments['ready'],
            mode: $this->arguments['mode'],
            readonly: $this->arguments['readonly'],
            globalDocument: $this->arguments['globalDocument'],
            documentType: $this->arguments['documentType'],
            includes: $this->arguments['includes'],
            parameters: $this->arguments['routeParameters'],
            contextIdentifier: $this->arguments['contextIdentifier'],
            uid: $this->arguments['uid'],
            documentName: $this->arguments['documentName'],
            contextType: $this->arguments['contextType']
        );

        $attributeMarkupList = [];
        foreach ($attributes as $name => $value) {
            $attributeMarkupList[] = sprintf('data-%s="%s"', $name, htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
        }

        $additionalAttributes = $this->arguments['additionalAttributes'];
        foreach ($additionalAttributes as $name => $value) {
            $attributeMarkupList[$name] = sprintf('%s="%s"', $name, htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
        }

        return '<textarea ' . implode(' ', $attributeMarkupList) . '></textarea>';
    }
}
