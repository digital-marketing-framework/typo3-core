<?php

namespace DigitalMarketingFramework\Typo3\Core\Registry\Event;

use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Core\Registry\RegistryUpdateType;

class CoreRegistryUpdateEvent
{
    public function __construct(
        protected RegistryInterface $registry,
        protected RegistryUpdateType $type,
    ) {
    }

    public function getRegistry(): RegistryInterface
    {
        return $this->registry;
    }

    public function getUpdateType(): RegistryUpdateType
    {
        return $this->type;
    }
}
