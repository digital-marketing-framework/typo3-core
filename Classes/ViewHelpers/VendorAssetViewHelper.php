<?php

namespace DigitalMarketingFramework\Typo3\Core\ViewHelpers;

use Closure;
use DigitalMarketingFramework\Typo3\Core\Utility\VendorAssetUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class VendorAssetViewHelper extends AbstractViewHelper
{
    public function initializeArguments()
    {
        $this->registerArgument('package', 'string', 'composer package name');
        $this->registerArgument('path', 'string', 'package path to asset');
        $this->registerArgument('returnUrl', 'bool', 'return the url of the resulting asset');
    }

    public static function renderStatic(
        array $arguments,
        Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): string {
        $url = VendorAssetUtility::makeVendorAssetAvailable($arguments['package'], $arguments['path']);
        if ($arguments['returnUrl'] ?? true) {
            return $url;
        }
        return '';
    }
}
