<?php

use DigitalMarketingFramework\Typo3\Core\Controller\BackendOverviewController;
use DigitalMarketingFramework\Typo3\Core\Controller\Event\BackendControllerUpdateEvent;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3') || die();

(static function () {
    $eventDispatcher = GeneralUtility::makeInstance(EventDispatcher::class);
    $controllerEvent = new BackendControllerUpdateEvent();
    $controllerEvent->addControllerActions(BackendOverviewController::class, ['show']);
    $eventDispatcher->dispatch($controllerEvent);

    ExtensionUtility::registerModule(
        'DigitalMarketingFramework',
        'web',
        'manager',
        '',
        $controllerEvent->getControllersAndActions(),
        [
            'access' => 'user,group',
            'icon'   => 'EXT:digitalmarketingframework/Resources/Public/Icons/Extension.svg',
            'labels' => 'LLL:EXT:digitalmarketingframework/Resources/Private/Language/locallang_manager.xlf',
            'navigationComponentId' => '',
            'inheritNavigationComponentFromMainModule' => false,
        ]
    );
})();
