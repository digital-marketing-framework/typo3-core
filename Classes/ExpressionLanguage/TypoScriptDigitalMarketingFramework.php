<?php

namespace DigitalMarketingFramework\Typo3\Core\ExpressionLanguage;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

class TypoScriptDigitalMarketingFramework
{
    public function extensionLoaded($extKey)
    {
        return ExtensionManagementUtility::isLoaded($extKey);
    }
}
