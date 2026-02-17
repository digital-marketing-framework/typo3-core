<?php

namespace DigitalMarketingFramework\Typo3\Core\Backend\UriRouteResolver;

class TestsEditUriRouteResolver extends Typo3EditRecordUriRouteResolver
{
    protected function getRouteMatch(): string
    {
        return 'page.tests.edit';
    }

    protected function getTableName(): string
    {
        return 'tx_dmfcore_domain_model_test_case';
    }
}
