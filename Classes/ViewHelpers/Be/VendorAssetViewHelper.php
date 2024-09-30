<?php

namespace DigitalMarketingFramework\Typo3\Core\ViewHelpers\Be;

use Closure;
use DigitalMarketingFramework\Typo3\Core\Registry\RegistryCollection;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class VendorAssetViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('path', 'string', 'package path to asset');
        $this->registerArgument('returnUrl', 'bool', 'return the url of the resulting asset', false, true);
    }

    public static function renderStatic(
        array $arguments,
        Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext,
    ): string {
        $registryCollection = GeneralUtility::makeInstance(RegistryCollection::class);
        $registry = $registryCollection->getRegistry();
        $assetService = $registry->getAssetService();

        $url = $assetService->makeAssetPublic($arguments['path']);

        if ((bool)$arguments['returnUrl']) {
            return $url;
        }

        return '';
    }
}
