<?php

namespace DigitalMarketingFramework\Typo3\Core\ExpressionLanguage;

use TYPO3\CMS\Core\ExpressionLanguage\AbstractProvider;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class TypoScriptConditionProvider extends AbstractProvider
{
    public function __construct()
    {
        $this->expressionLanguageVariables = [
            'digitalMarketingFramework' => GeneralUtility::makeInstance(TypoScriptDigitalMarketingFramework::class),
        ];
    }
}
