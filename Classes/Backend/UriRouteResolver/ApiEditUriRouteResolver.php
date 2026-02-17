<?php

namespace DigitalMarketingFramework\Typo3\Core\Backend\UriRouteResolver;

class ApiEditUriRouteResolver extends Typo3EditRecordUriRouteResolver
{
    protected function getRouteMatch(): string
    {
        return 'page.api.edit';
    }

    protected function getTableName(): string
    {
        return 'tx_dmfcore_domain_model_api_endpoint';
    }
}
