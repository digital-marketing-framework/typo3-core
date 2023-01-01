<?php

namespace DigitalMarketingFramework\Typo3\Core\Registry\Event;

use DigitalMarketingFramework\Core\Registry\RegistryInterface;

abstract class CoreRegistryUpdateEvent
{
    public function __construct(
        protected RegistryInterface $registry,
    ) {
    }

    public function getRegistry(): RegistryInterface
    {
        return $this->registry;
    }
}
