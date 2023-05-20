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
        $this->registerArgument('src', 'string', 'path to the vendor asset');
    }

    public static function renderStatic(
        array $arguments,
        Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): string {
        return VendorAssetUtility::makeVendorAssetAvailable($arguments['src']);
    }
}
