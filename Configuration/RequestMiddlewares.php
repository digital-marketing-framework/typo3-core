<?php

use DigitalMarketingFramework\Typo3\Core\Middleware\RestMiddleware;

return [
    'frontend' => [
        'digital-marketing-framework-rest' => [
            'target' => RestMiddleware::class,
            'description' => 'Handling DMF API Requests',
            'after' => [
                'typo3/cms-frontend/prepare-tsfe-rendering',
            ],
            'before' => [
                'typo3/cms-frontend/shortcut-and-mountpoint-redirect',
            ],
        ],
    ],
];
