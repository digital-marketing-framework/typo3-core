<?php

namespace DigitalMarketingFramework\Typo3\Core\Registry;

use DigitalMarketingFramework\Core\Registry\ProxyArgument;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Proxy argument that lazily resolves a class via TYPO3's GeneralUtility::makeInstance().
 *
 * Use this instead of ProxyArgument when you want to inject a TYPO3-managed service
 * as a plugin constructor argument without specifying the resolution callable each time.
 */
class Typo3ProxyArgument extends ProxyArgument
{
    /**
     * @param class-string $class
     */
    public function __construct(string $class)
    {
        parent::__construct(static fn () => GeneralUtility::makeInstance($class));
    }
}
