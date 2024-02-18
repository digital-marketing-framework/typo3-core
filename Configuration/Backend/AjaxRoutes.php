<?php

use DigitalMarketingFramework\Typo3\Core\Controller\ConfigurationDocumentAjaxController;
use DigitalMarketingFramework\Typo3\Core\Controller\GlobalConfigurationAjaxController;

return [
    // configuration document
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

    // global configuration
    'digitalmarketingframework_globalconfiguration_schema' => [
        'path' => '/digital-marketing-framework-backend/global-configuration/schema',
        'target' => GlobalConfigurationAjaxController::class . '::schemaAction',
    ],
    'digitalmarketingframework_globalconfiguration_defaults' => [
        'path' => '/digital-marketing-framework-backend/global-configuration/defaults',
        'target' => GlobalConfigurationAjaxController::class . '::defaultsAction',
    ],
    'digitalmarketingframework_globalconfiguration_merge' => [
        'path' => '/digital-marketing-framework-backend/global-configuration/merge',
        'target' => GlobalConfigurationAjaxController::class . '::mergeAction',
    ],
    'digitalmarketingframework_globalconfiguration_split' => [
        'path' => '/digital-marketing-framework-backend/global-configuration/split',
        'target' => GlobalConfigurationAjaxController::class . '::splitAction',
    ],
];
