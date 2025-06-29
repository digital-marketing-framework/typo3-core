<?php

namespace DigitalMarketingFramework\Typo3\Core\Registry\EventListener;

use DigitalMarketingFramework\Typo3\Core\Typo3CoreInitialization;

class CoreRegistryUpdateEventListener extends AbstractCoreRegistryUpdateEventListener
{
    public function __construct(Typo3CoreInitialization $initialization) {
        parent::__construct($initialization);
    }
}
