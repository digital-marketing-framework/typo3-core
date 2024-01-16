<?php

namespace DigitalMarketingFramework\Typo3\Core\Controller;

use DigitalMarketingFramework\Typo3\Core\Controller\Event\FrontendJavaScriptSettingsUpdateEvent;
use DigitalMarketingFramework\Typo3\Core\Controller\Event\FrontendJavaScriptUpdateEvent;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\RootlineUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class FrontendController extends ActionController
{
    protected function getPageId(): int
    {
        return $this->request->getAttribute('routing')->getPageId();
    }

    protected function getRootPageId(int $pageId): int
    {
        $rootlineObject = GeneralUtility::makeInstance(RootlineUtility::class, $pageId);
        $rootline = $rootlineObject->get();

        return $rootline !== [] ? array_pop($rootline)['uid'] : $pageId;
    }

    public function javaScriptSettingsAction(): ResponseInterface
    {
        $pageId = $this->getPageId();
        $rootPageId = $this->getRootPageId($pageId);
        $event = new FrontendJavaScriptSettingsUpdateEvent($pageId, $rootPageId);
        $this->eventDispatcher->dispatch($event);
        $this->view->assign('DMF', $event->getSettings());

        $event = new FrontendJavaScriptUpdateEvent();
        $this->eventDispatcher->dispatch($event);
        $this->view->assign('scripts', $event->getFrontendScripts());

        return $this->htmlResponse();
    }
}
