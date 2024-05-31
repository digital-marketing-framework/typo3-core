<?php

namespace DigitalMarketingFramework\Typo3\Core\Registry;

use DigitalMarketingFramework\Core\Registry\RegistryCollection as OriginalRegistryCollection;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;

class RegistryCollection extends OriginalRegistryCollection
{
    public function __construct(EventDispatcher $eventDispatcher)
    {
        parent::__construct();
        $eventDispatcher->dispatch($this);
    }
}
