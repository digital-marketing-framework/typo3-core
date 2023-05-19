<?php

namespace DigitalMarketingFramework\Typo3\Core\Registry;

use DigitalMarketingFramework\Core\Registry\Registry as CoreRegistry;
use DigitalMarketingFramework\Typo3\Core\Registry\Event\CoreRegistryGlobalConfigurationUpdateEvent;
use DigitalMarketingFramework\Typo3\Core\Registry\Event\CoreRegistryPluginUpdateEvent;
use DigitalMarketingFramework\Typo3\Core\Registry\Event\CoreRegistryServiceUpdateEvent;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\SingletonInterface;

class Registry extends CoreRegistry implements SingletonInterface
{
    public function __construct(
        protected EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function initializeObject(): void
    {
        $this->eventDispatcher->dispatch(
            new CoreRegistryGlobalConfigurationUpdateEvent($this)
        );
        $this->eventDispatcher->dispatch(
            new CoreRegistryServiceUpdateEvent($this)
        );
        $this->eventDispatcher->dispatch(
            new CoreRegistryPluginUpdateEvent($this)
        );
    }
}
