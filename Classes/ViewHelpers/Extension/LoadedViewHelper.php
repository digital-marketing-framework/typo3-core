<?php

namespace DigitalMarketingFramework\Typo3\Core\ViewHelpers\Extension;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class LoadedViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('extKey', 'string', 'Extension key to check', true);
    }

    public function render(): bool
    {
        $extKey = (string)$this->arguments['extKey'];

        return ExtensionManagementUtility::isLoaded($extKey);
    }
}
