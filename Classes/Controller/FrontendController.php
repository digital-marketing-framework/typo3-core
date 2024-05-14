<?php

namespace DigitalMarketingFramework\Typo3\Core\Controller;

use DigitalMarketingFramework\Typo3\Core\Api\Event\FrontendJavaScriptSettingsUpdateEvent;
use DigitalMarketingFramework\Typo3\Core\Api\Event\FrontendJavaScriptUpdateEvent;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\RootlineUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class FrontendController extends ActionController
{
    public function javaScriptSettingsAction(): ResponseInterface
    {
        $event = new FrontendJavaScriptSettingsUpdateEvent();
        $this->eventDispatcher->dispatch($event);
        $this->view->assign('DMF', $event->getSettings());

        $event = new FrontendJavaScriptUpdateEvent();
        $this->eventDispatcher->dispatch($event);
        $this->view->assign('scripts', $event->getFrontendScripts());

        return $this->htmlResponse();
    }
}
