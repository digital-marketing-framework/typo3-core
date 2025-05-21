<?php

use DigitalMarketingFramework\Typo3\Core\Controller\BackendModuleController;

return [
    'digitalmarketingframework_json' => [
        'path' => '/digital-marketing-framework-backend/json',
        'target' => BackendModuleController::class . '::handleRequest',
    ],
];
