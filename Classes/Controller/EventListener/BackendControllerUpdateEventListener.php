<?php

namespace DigitalMarketingFramework\Typo3\Core\Controller\EventListener;

use DigitalMarketingFramework\Typo3\Core\Controller\ConfigurationDocumentController;
use DigitalMarketingFramework\Typo3\Core\Controller\Event\BackendControllerUpdateEvent;

class BackendControllerUpdateEventListener
{
    public function __invoke(BackendControllerUpdateEvent $event)
    {
        $event->addControllerActions(ConfigurationDocumentController::class, ['list', 'edit', 'save', 'create', 'delete']);
    }
}
