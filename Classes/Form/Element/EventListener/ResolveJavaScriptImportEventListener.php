<?php

namespace DigitalMarketingFramework\Typo3\Core\Form\Element\EventListener;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Typo3\Core\Form\Element\ConfigurationEditorTextFieldElement;
use DigitalMarketingFramework\Typo3\Core\Registry\RegistryCollection;
use TYPO3\CMS\Core\Information\Typo3Version;
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
                    // v13: JavaScriptRenderer concatenates $sitePath ('/') with this URL — return path
                    //      without a leading slash so the final src becomes '/typo3temp/...'.
                    // v14: JavaScriptRenderer uses the URL as-is in the script src — and backend pages
                    //      now have a '/record/' segment, so a relative path would resolve wrong. Return
                    //      a root-relative URL with a leading slash.
                    if ((new Typo3Version())->getMajorVersion() >= 14) {
                        $path = '/' . ltrim($path, '/');
                    }

                    $event->resolution = $path;
                }
            } catch (DigitalMarketingFrameworkException) {
            }
        }
    }
}
