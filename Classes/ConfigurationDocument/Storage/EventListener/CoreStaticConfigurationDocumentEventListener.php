<?php

namespace DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Storage\EventListener;

class CoreStaticConfigurationDocumentEventListener extends StaticConfigurationDocumentEventListener
{
    protected function getExtensionKey(): string
    {
        return 'dmf_core';
    }
}
