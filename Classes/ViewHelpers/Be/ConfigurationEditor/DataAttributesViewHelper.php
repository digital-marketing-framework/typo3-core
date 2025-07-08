<?php

namespace DigitalMarketingFramework\Typo3\Core\ViewHelpers\Be\ConfigurationEditor;

use DigitalMarketingFramework\Core\ConfigurationEditor\MetaData;
use DigitalMarketingFramework\Typo3\Core\Registry\RegistryCollection;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\ViewHelpers\Be\AbstractBackendViewHelper;

class DataAttributesViewHelper extends AbstractBackendViewHelper
{
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
    }

    /**
     * @return array<string,string>
     */
    public function render(): array
    {
        return GeneralUtility::makeInstance(RegistryCollection::class)->getRegistry()->getBackendRenderingService()->getTextAreaDataAttributes(
            ready: $this->arguments['ready'],
            mode: $this->arguments['mode'],
            readonly: $this->arguments['readonly'],
            globalDocument: $this->arguments['globalDocument'],
            documentType: $this->arguments['documentType'],
            includes: $this->arguments['includes'],
            parameters: $this->arguments['routeParameters'],
            contextIdentifier: $this->arguments['contextIdentifier'],
            uid: $this->arguments['uid']
        );
    }
}
