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
        $this->registerArgument('baseRoute', 'string', 'Base route for ajax URLs', false, 'configuration');
        $this->registerArgument('includes', 'bool', 'Configuration support includes', false, true);
        $this->registerArgument('routeParameters', 'array', 'Additional AJAX route parameters', false, []);
    }

    /**
     * @return array<string,string>
     */
    public function render(): array
    {
        return ConfigurationEditorRenderUtility::getTextAreaDataAttributes(
            ready: $this->arguments['ready'],
            mode: $this->arguments['mode'],
            readonly: $this->arguments['readonly'],
            globalDocument: $this->arguments['globalDocument'],
            baseRoute: $this->arguments['baseRoute'],
            includes: $this->arguments['includes'],
            parameters: $this->arguments['routeParameters']
        );
    }
}
