<?php

namespace DigitalMarketingFramework\Typo3\Core\Backend\Controller\SectionController;

use DigitalMarketingFramework\Core\Registry\RegistryInterface;

class ApiEditSectionController extends EditSectionController
{
    public const WEIGHT = 0;

    public function __construct(string $keyword, RegistryInterface $registry)
    {
        parent::__construct($keyword, $registry, 'api');
    }

    protected function getTableName(): string
    {
        return 'tx_dmfcore_domain_model_api_endpoint';
    }
}
