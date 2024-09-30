<?php

namespace DigitalMarketingFramework\Typo3\Core\Registry;

use DigitalMarketingFramework\Core\Registry\RegistryCollection as OriginalRegistryCollection;
use DigitalMarketingFramework\Typo3\Core\Context\Typo3RequestContext;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;

class RegistryCollection extends OriginalRegistryCollection
{
    public function __construct(
        protected EventDispatcher $eventDispatcher,
        Typo3RequestContext $context,
    ) {
        parent::__construct();
        $this->setContext($context);
    }

    protected function fetchRegistries(): void
    {
        $this->eventDispatcher->dispatch($this);
    }
}
