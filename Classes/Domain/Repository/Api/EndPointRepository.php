<?php

namespace DigitalMarketingFramework\Typo3\Core\Domain\Repository\Api;

use DigitalMarketingFramework\Core\Api\EndPoint\EndPointSchema;
use DigitalMarketingFramework\Core\Api\EndPoint\EndPointStorageInterface;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationInterface;
use DigitalMarketingFramework\Core\Model\Api\EndPoint;
use DigitalMarketingFramework\Core\Model\Api\EndPointInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Typo3\Core\Api\GlobalConfiguration\Settings\EndPointSettings;
use DigitalMarketingFramework\Typo3\Core\Domain\Repository\ItemStorageRepository;
use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 * @extends ItemStorageRepository<EndPointInterface>
 */
class EndPointRepository extends ItemStorageRepository implements EndPointStorageInterface
{
    public function __construct(ConnectionPool $connectionPool)
    {
        parent::__construct($connectionPool, EndPoint::class, 'tx_dmfcore_domain_model_api_endpoint');
    }

    public function getPid(): int
    {
        if ($this->pid === null) {
            if ($this->globalConfiguration instanceof GlobalConfigurationInterface) {
                $endPointSettings = $this->globalConfiguration->getGlobalSettings(EndPointSettings::class);
                $this->pid = $endPointSettings->getPid();
            } else {
                $this->pid = 0;
            }
        }

        return $this->pid;
    }

    public function fetchByName(string $name): ?EndPointInterface
    {
        return $this->fetchOneFiltered(['name' => $name]);
    }

    public static function getSchema(): ContainerSchema
    {
        return new EndPointSchema();
    }
}
