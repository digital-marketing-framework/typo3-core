<?php

namespace DigitalMarketingFramework\Typo3\Core\ViewHelpers\Be\ConfigurationEditor;

use DigitalMarketingFramework\Typo3\Core\Utility\ConfigurationEditorRenderUtility;
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
    }

    public function render(): array
    {
        return ConfigurationEditorRenderUtility::getTextAreaDataAttributes(
            $this->arguments['ready'],
            $this->arguments['mode'],
            $this->arguments['readonly'],
            $this->arguments['globalDocument']
        );
    }
}
