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
    'web_DmfCoreManager' => [
        'parent' => 'web',
        'inheritNavigationComponentFromMainModule' => false,
        'access' => 'admin',
        'workspaces' => 'live',
        'icon'   => 'EXT:dmf_core/Resources/Public/Icons/Extension.svg',
        'path' => '/module/web/DmfCoreManager',
        'labels' => 'LLL:EXT:dmf_core/Resources/Private/Language/locallang_manager.xlf',
        'extensionName' => 'DmfCore',
        'controllerActions' => $controllerEvent->getControllersAndActions(),
    ],
    'digitalmarketingframework_admin' => [
        'parent' => 'web',
        'inheritNavigationComponentFromMainModule' => false,
        'position' => ['top'],
        'access' => 'admin',
        'workspaces' => 'live',
        'path' => '/module/web/digital-marketing-framework',
        'labels' => 'LLL:EXT:dmf_core/Resources/Private/Language/locallang_manager.xlf',
        'icon'   => 'EXT:dmf_core/Resources/Public/Icons/Extension.svg',
        'routes' => [
            '_default' => [
                'target' => \DigitalMarketingFramework\Typo3\Core\Controller\BackendModuleController::class . '::handleRequest',
            ],
        ],
    ],
];
