<?php

use DigitalMarketingFramework\Typo3\Core\Controller\ConfigurationDocumentAjaxController;

return [
    'digitalmarketingframework_configuration_schema' => [
        'path' => '/digital-marketing-framework-backend/schema',
        'target' => ConfigurationDocumentAjaxController::class . '::metaDataAction',
    ],
];
