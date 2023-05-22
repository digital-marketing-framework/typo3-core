<?php

use DigitalMarketingFramework\Typo3\Core\Controller\ConfigurationDocumentAjaxController;

return [
    'digitalmarketingframework_configuration_schema' => [
        'path' => '/digital-marketing-framework-backend/configuration-document/schema',
        'target' => ConfigurationDocumentAjaxController::class . '::schemaAction',
    ],
    'digitalmarketingframework_configuration_defaults' => [
        'path' => '/digital-marketing-framework-backend/configuration-document/defaults',
        'target' => ConfigurationDocumentAjaxController::class . '::defaultsAction',
    ],
    'digitalmarketingframework_configuration_merge' => [
        'path' => '/digital-marketing-framework-backend/configuration-document/merge',
        'target' => ConfigurationDocumentAjaxController::class . '::mergeAction',
    ],
    'digitalmarketingframework_configuration_split' => [
        'path' => '/digital-marketing-framework-backend/configuration-document/split',
        'target' => ConfigurationDocumentAjaxController::class . '::splitAction',
    ],
    'digitalmarketingframework_configuration_update_includes' => [
        'path' => '/digital-marketing-framework-backend/configuration-document/update-includes',
        'target' => ConfigurationDocumentAjaxController::class . '::updateIncludesAction',
    ],
];
