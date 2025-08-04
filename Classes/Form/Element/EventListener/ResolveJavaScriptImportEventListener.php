<?php

namespace DigitalMarketingFramework\Typo3\Core\Form\Element\EventListener;

use DigitalMarketingFramework\Core\Resource\Asset\AssetService;
use DigitalMarketingFramework\Typo3\Core\Form\Element\ConfigurationEditorTextFieldElement;
use TYPO3\CMS\Core\Page\Event\ResolveJavaScriptImportEvent;

class ResolveJavaScriptImportEventListener
{
    public function __invoke(ResolveJavaScriptImportEvent $event): void
    {
        if (str_starts_with($event->specifier, ConfigurationEditorTextFieldElement::JS_VENDOR . '/typo3temp/' . AssetService::TEMP_PATH_ASSETS . '/')) {
            $event->resolution = substr($event->specifier, strlen(ConfigurationEditorTextFieldElement::JS_VENDOR) + 1);
        }
    }
}
