<?php

namespace DigitalMarketingFramework\Typo3\Core\Form\Element\EventListener;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Typo3\Core\Form\Element\ConfigurationEditorTextFieldElement;
use DigitalMarketingFramework\Typo3\Core\Registry\RegistryCollection;
use TYPO3\CMS\Core\Page\Event\ResolveJavaScriptImportEvent;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ResolveJavaScriptImportEventListener
{
    public function __invoke(ResolveJavaScriptImportEvent $event): void
    {
        if (str_starts_with($event->specifier, ConfigurationEditorTextFieldElement::JS_VENDOR . '/PKG:')) {
            try {
                $registryCollection = GeneralUtility::makeInstance(RegistryCollection::class);
                $assetService = $registryCollection->getRegistry()->getAssetService();
                $inputPath = substr($event->specifier, strlen(ConfigurationEditorTextFieldElement::JS_VENDOR) + 1);
                $path = $assetService->makeAssetPublic($inputPath);

                if ($path !== null) {
                    $event->resolution = $path;
                }
            } catch (DigitalMarketingFrameworkException) {
            }
        }
    }
}
