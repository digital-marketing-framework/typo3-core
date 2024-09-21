<?php

namespace DigitalMarketingFramework\Typo3\Core\Registry;

use DigitalMarketingFramework\Core\Registry\RegistryCollection as OriginalRegistryCollection;
use DigitalMarketingFramework\Typo3\Core\Context\Typo3RequestContext;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;

class RegistryCollection extends OriginalRegistryCollection
{
    public function __construct(EventDispatcher $eventDispatcher, Typo3RequestContext $context)
    {
        parent::__construct();
        $eventDispatcher->dispatch($this);
        foreach ($this->collection as $registry) {
            $registry->init();
        }

        $this->setContext($context);
    }
}
