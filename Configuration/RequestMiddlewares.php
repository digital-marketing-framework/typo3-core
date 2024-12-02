<?php

use DigitalMarketingFramework\Typo3\Core\Middleware\RestMiddleware;

$list = [
    0 => 'typo3/cms-frontend/timetracker',
    1 => 'typo3/cms-core/verify-host-header',
    2 => 'fluidtypo3/vhs/request-availability',
    3 => 'typo3/cms-core/normalized-params-attribute',

    4 => 'typo3/cms-frontend/eid',
    5 => 'typo3/cms-frontend/site',

    6 => 'typo3/cms-frontend/maintenance-mode',
    7 => 'typo3/cms-core/request-token-middleware',
    8 => 'typo3/cms-frontend/backend-user-authentication',
    9 => 'typo3/cms-frontend/authentication',
    10 => 'b13/just-in-case',
    11 => 'typo3/cms-redirects/redirecthandler',
    12 => 'typo3/cms-frontend/base-redirect-resolver',
    13 => 'typo3/cms-frontend/csp-report',
    14 => 'typo3/cms-frontend/static-route-resolver',
    15 => 'typo3/cms-workspaces/preview',
    16 => 'typo3/cms-frontend/page-resolver',

    17 => 'typo3/cms-frontend/page-argument-validator',
    18 => 'typo3/cms-workspaces/preview-permissions',
    19 => 'typo3/cms-frontend/preview-simulator',
    20 => 'typo3/cms-frontend/tsfe',
    21 => 'typo3/cms-frontend/prepare-tsfe-rendering',

    22 => 'typo3/cms-frontend/shortcut-and-mountpoint-redirect',
    23 => 'typo3/cms-frontend/content-length-headers',
    24 => 'typo3/cms-frontend/csp-headers',
    25 => 'typo3/cms-frontend/output-compression',
    26 => 'typo3/cms-core/response-propagation',
    27 => 'fluidtypo3/vhs/asset-inclusion',
    28 => 'site/fastly',
];
// $index = 21; // > yep

// $index = 15; // > nope
// $index = 16; // > yep

// $index = 15; // > yep, without route enhancer
// $index = 16; // > yep, without route enchancer

$index = 16;


return [
    'frontend' => [
        'digital-marketing-framework-rest' => [
            'target' => RestMiddleware::class,
            'description' => 'Handling DMF API Requests',
            'after' => [
                // 'typo3/cms-frontend/prepare-tsfe-rendering',
                // 'typo3/cms-frontend/site',
                // 'typo3/cms-frontend/static-route-resolver',
                $list[$index],
                // 'typo3/cms-frontend/page-resolver',
                // 'typo3/cms-frontend/preview-simulator',
            ],
            'before' => [
                // 'typo3/cms-frontend/shortcut-and-mountpoint-redirect',
                // 'typo3/cms-frontend/maintenance-mode',
                // 'typo3/cms-workspaces/preview',
                $list[$index + 1],
                // 'typo3/cms-frontend/page-resolver',
                // 'typo3/cms-frontend/page-argument-validator',
                // 'typo3/cms-frontend/tsfe',
            ],
        ],
    ],
];
