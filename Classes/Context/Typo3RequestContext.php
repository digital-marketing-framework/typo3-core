<?php

namespace DigitalMarketingFramework\Typo3\Core\Context;

use DigitalMarketingFramework\Core\Context\RequestContext;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class Typo3RequestContext extends RequestContext
{
    public function getRequestVariable(string $name): string
    {
        return GeneralUtility::getIndpEnv($name);
    }

    public function getRequestArgument(string $name): mixed
    {
        return GeneralUtility::_GET($name);
    }

    public function getRequestArguments(): array
    {
        return GeneralUtility::_GET();
    }
}
