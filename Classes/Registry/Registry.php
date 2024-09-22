<?php

namespace DigitalMarketingFramework\Typo3\Core\Registry;

use DigitalMarketingFramework\Core\Registry\Registry as CoreRegistry;
use DigitalMarketingFramework\Core\Registry\RegistryUpdateType;
use DigitalMarketingFramework\Typo3\Core\Registry\Event\CoreRegistryUpdateEvent;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\SingletonInterface;

class Registry extends CoreRegistry implements SingletonInterface
{
    public function __construct(
        protected EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function init(): void
    {
        $this->eventDispatcher->dispatch(
            new CoreRegistryUpdateEvent($this, RegistryUpdateType::GLOBAL_CONFIGURATION)
        );
        $this->eventDispatcher->dispatch(
            new CoreRegistryUpdateEvent($this, RegistryUpdateType::SERVICE)
        );
        $this->eventDispatcher->dispatch(
            new CoreRegistryUpdateEvent($this, RegistryUpdateType::PLUGIN)
        );
    }
}
