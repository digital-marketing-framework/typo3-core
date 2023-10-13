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
        'DmfCore',
        'web',
        'manager',
        '',
        $controllerEvent->getControllersAndActions(true),
        [
            'access' => 'user,group',
            'icon'   => 'EXT:dmf_core/Resources/Public/Icons/Extension.svg',
            'labels' => 'LLL:EXT:dmf_core/Resources/Private/Language/locallang_manager.xlf',
            'navigationComponentId' => '',
            'inheritNavigationComponentFromMainModule' => false,
        ]
    );
})();
