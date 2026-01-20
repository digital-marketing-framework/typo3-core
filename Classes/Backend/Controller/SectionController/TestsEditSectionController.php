<?php

namespace DigitalMarketingFramework\Typo3\Core\Backend\Controller\SectionController;

use DigitalMarketingFramework\Core\Registry\RegistryInterface;

class TestsEditSectionController extends EditSectionController
{
    /**
     * @var int
     */
    public const WEIGHT = 0;

    public function __construct(string $keyword, RegistryInterface $registry)
    {
        parent::__construct($keyword, $registry, 'tests');
    }

    protected function getTableName(): string
    {
        return 'tx_dmfcore_domain_model_test_case';
    }
}
