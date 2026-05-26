<?php

namespace DigitalMarketingFramework\Typo3\Core\ViewHelpers\Be;

use DigitalMarketingFramework\Typo3\Core\Registry\RegistryCollection;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class VendorAssetViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('path', 'string', 'package path to asset');
        $this->registerArgument('returnUrl', 'bool', 'return the url of the resulting asset', false, true);
    }

    public function render(): string
    {
        $registryCollection = GeneralUtility::makeInstance(RegistryCollection::class);
        $registry = $registryCollection->getRegistry();
        $assetService = $registry->getAssetService();

        $url = $assetService->makeAssetPublic($this->arguments['path']);

        if ((bool)$this->arguments['returnUrl']) {
            return $url;
        }

        return '';
    }
}
