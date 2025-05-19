<?php

use DigitalMarketingFramework\Typo3\Core\Controller\BackendModuleController;

return [
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
                'target' => BackendModuleController::class . '::handleRequest',
            ],
        ],
    ],
];
