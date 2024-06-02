<?php

namespace DigitalMarketingFramework\Typo3\Core\Controller\EventListener;

use DigitalMarketingFramework\Typo3\Core\Controller\ConfigurationDocumentController;
use DigitalMarketingFramework\Typo3\Core\Controller\Event\BackendControllerUpdateEvent;
use DigitalMarketingFramework\Typo3\Core\Controller\GlobalConfigurationController;

class BackendControllerUpdateEventListener
{
    public function __invoke(BackendControllerUpdateEvent $event): void
    {
        $event->addControllerActions(ConfigurationDocumentController::class, ['list', 'edit', 'save', 'create', 'delete']);
        $event->addControllerActions(GlobalConfigurationController::class, ['edit', 'save']);
    }
}
