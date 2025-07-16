<?php

namespace DigitalMarketingFramework\Typo3\Core\Domain\Repository;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Model\ItemInterface;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\ParameterType;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;

/**
 * @template ItemClass of ItemInterface
 *
 * @extends ItemStorage<ItemClass>
 */
abstract class ItemStorageRepository extends ItemStorage
{
    protected ?int $pid = null;

    public function getPid(): int
    {
        if ($this->pid === null) {
            $this->pid = 0;
        }

        return $this->pid;
    }

    public function setPid(int $pid): void
    {
        $this->pid = $pid;
    }

    protected function prepareQuery(QueryBuilder $queryBuilder): void
    {
        $queryBuilder->where($queryBuilder->expr()->eq('pid', $this->getPid()));
    }

    protected function prepareAdd($item): array
    {
        $data = parent::getItemData($item);
        $data['pid'] = $this->getPid();
        return $data;
    }

    protected function prepareUpdate($item, QueryBuilder $queryBuilder): void
    {
        $queryBuilder->set('pid', $this->getPid());
    }
}
