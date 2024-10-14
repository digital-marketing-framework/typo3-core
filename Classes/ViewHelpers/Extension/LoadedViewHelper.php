<?php

namespace DigitalMarketingFramework\Typo3\Core\ViewHelpers\Extension;

use DigitalMarketingFramework\Collector\Core\ContentModifier\ContentModifierHandlerInterface;
use DigitalMarketingFramework\Collector\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Typo3\Core\Registry\RegistryCollection;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
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
