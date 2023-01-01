<?php

namespace DigitalMarketingFramework\Typo3\Core\Registry\EventListener;

use DigitalMarketingFramework\Core\CorePluginInitialization;
use DigitalMarketingFramework\Typo3\Core\Registry\Event\CoreRegistryPluginUpdateEvent;

class CoreRegistryPluginUpdateEventListener
{
    public function __invoke(CoreRegistryPluginUpdateEvent $event): void
    {
        CorePluginInitialization::initialize($event->getRegistry());
    }
}
