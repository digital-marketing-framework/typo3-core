<?php

namespace DigitalMarketingFramework\Typo3\Core\Registry\EventListener;

use DigitalMarketingFramework\Typo3\Core\GlobalConfiguration\GlobalConfiguration;
use DigitalMarketingFramework\Typo3\Core\Registry\Event\CoreRegistryGlobalConfigurationUpdateEvent;

class CoreRegistryGlobalConfigurationUpdateEventListener
{
    public function __construct(
        protected GlobalConfiguration $globalConfiguration,
    ) {}

    public function __invoke(CoreRegistryGlobalConfigurationUpdateEvent $event): void
    {
        $event->getRegistry()->setGlobalConfiguration($this->globalConfiguration);
    }
}
