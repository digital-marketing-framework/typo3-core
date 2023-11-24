<?php

namespace DigitalMarketingFramework\Typo3\Core\Controller\EventListener;

use DigitalMarketingFramework\Typo3\Core\Controller\Event\FrontendJavaScriptUpdateEvent;
use DigitalMarketingFramework\Typo3\Core\Registry\Registry;

class FrontendJavaScriptUpdateEventListener
{
    public function __construct(protected Registry $registry)
    {
    }

    public function __invoke(FrontendJavaScriptUpdateEvent $event): void
    {
        $event->processRegistry($this->registry);
    }
}
