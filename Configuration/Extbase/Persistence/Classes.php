<?php

declare(strict_types=1);

use DigitalMarketingFramework\Typo3\Core\Domain\Model\Api\EndPoint;
use DigitalMarketingFramework\Typo3\Core\Domain\Model\TestCase\TestCase;

return [
    EndPoint::class => [
        'tableName' => 'tx_dmfcore_domain_model_api_endpoint',
    ],
    TestCase::class => [
        'tableName' => 'tx_dmfcore_domain_model_test_case',
    ],
];
