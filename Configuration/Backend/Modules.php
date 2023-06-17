<?php

use DigitalMarketingFramework\Typo3\Core\Controller\BackendOverviewController;
use DigitalMarketingFramework\Typo3\Core\Controller\Event\BackendControllerUpdateEvent;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Core\Utility\GeneralUtility;

$eventDispatcher = GeneralUtility::makeInstance(EventDispatcher::class);
$controllerEvent = new BackendControllerUpdateEvent();
$controllerEvent->addControllerActions(BackendOverviewController::class, ['show']);
$eventDispatcher->dispatch($controllerEvent);

return [
    'web_DigitalMarketingFrameworkManager' => [
        'parent' => 'web',
        'inheritNavigationComponentFromMainModule' => false,
        'access' => 'admin',
        'workspaces' => 'live',
        'icon'   => 'EXT:digitalmarketingframework/Resources/Public/Icons/Extension.svg',
        'path' => '/module/web/DigitalMarketingFrameworkManager',
        'labels' => 'LLL:EXT:digitalmarketingframework/Resources/Private/Language/locallang_manager.xlf',
        'extensionName' => 'DigitalMarketingFramework',
        'controllerActions' => $controllerEvent->getControllersAndActions(),
    ],
];
