<?php

namespace DigitalMarketingFramework\Typo3\Core\Backend\UriRouteResolver;

use DigitalMarketingFramework\Core\Api\EndPoint\EndPointStorageAwareInterface;
use DigitalMarketingFramework\Core\Api\EndPoint\EndPointStorageAwareTrait;
use DigitalMarketingFramework\Core\Model\Api\EndPointInterface;
use DigitalMarketingFramework\Core\Model\ConfigurationDocument\DataSourceMigratable;
use DigitalMarketingFramework\Core\Model\DataSource\ApiEndPointDataSourceInterface;

class ApiEndPointDataSourceEditUriRouteResolver extends Typo3UriRouteResolver implements EndPointStorageAwareInterface
{
    use EndPointStorageAwareTrait;

    /**
     * @var int
     */
    public const WEIGHT = 0;

    protected function getRouteMatch(): string
    {
        return 'page.data-source.edit';
    }

    protected function match(string $route, array $arguments = []): bool
    {
        if (!parent::match($route, $arguments)) {
            return false;
        }

        $identifier = (string)($arguments['identifier'] ?? '');

        return str_starts_with($identifier, 'api:');
    }

    protected function doResolve(string $route, array $arguments = []): ?string
    {
        $identifier = (string)($arguments['identifier'] ?? '');
        $returnUrl = $this->getReturnUrl($arguments);

        // Try entity argument to avoid redundant lookup
        $entity = $arguments['entity'] ?? null;
        if ($entity instanceof DataSourceMigratable) {
            $dataSource = $entity->getDataSource();
            if ($dataSource instanceof ApiEndPointDataSourceInterface) {
                return $this->buildRecordEditUrl(
                    'tx_dmfcore_domain_model_api_endpoint',
                    $dataSource->getEndPoint()->getId(),
                    $returnUrl
                );
            }
        }

        // Fallback: look up endpoint by name
        $endPointName = substr($identifier, 4);
        $endPoint = $this->endPointStorage->fetchByName($endPointName);
        if ($endPoint instanceof EndPointInterface) {
            return $this->buildRecordEditUrl(
                'tx_dmfcore_domain_model_api_endpoint',
                $endPoint->getId(),
                $returnUrl
            );
        }

        return null;
    }
}
