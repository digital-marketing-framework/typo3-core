<?php

namespace DigitalMarketingFramework\Typo3\Core\Domain\Repository;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Model\ItemInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\Storage\ItemStorageInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;
use DigitalMarketingFramework\Typo3\Core\Domain\Model\Item;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;

/**
 * @template ItemClass of Item
 * @template ItemData of array<string,mixed>
 * @template Filters of array<string,mixed>
 *
 * @implements ItemStorageInterface<ItemClass,int,ItemData,Filters>
 */
abstract class ItemStorageRepository implements ItemStorageInterface
{
    protected int $pid = 0;

    /** @var array<string> */
    protected array $fields;

    /**
     * @param class-string<ItemClass> $itemClassName
     */
    public function __construct(
        protected ExtensionConfiguration $extensionConfiguration,
        protected ConnectionPool $connectionPool,
        protected string $itemClassName,
        protected string $tableName,
    ) {
        $this->fields = array_keys(static::getSchema()->getProperties());
    }

    public function create(?array $data = null)
    {
        $item = new $this->itemClassName();
        if ($data !== null) {
            $this->updateItem($item, $data);
        }

        return $item;
    }

    /**
     * @param ItemClass $item
     */
    protected function getItemMethod(ItemInterface $item, string $field, string $type): string
    {
        $method = $type . ucfirst(GeneralUtility::underscoredToCamelCase($field));

        if (!method_exists($item, $method)) {
            throw new DigitalMarketingFrameworkException(sprintf('Method "%s" not found in class "%s"', $field, get_class($item)));
        }

        return $method;
    }

    /**
     * @param ItemClass $item
     * @param ItemData $data
     */
    protected function updateItem($item, array $data): void
    {
        $fields = $this->fields;

        foreach ($fields as $field) {
            if (!isset($data[$field])) {
                continue;
            }

            $method = $this->getItemMethod($item, $field, 'set');
            $item->$method($data[$field]); // @phpstan-ignore-line dynamic method call based on item schema
        }

        if (isset($data['uid'])) {
            $item->setId($data['uid']);
        }
    }

    /**
     * @param ItemClass $item
     *
     * @return ItemData
     */
    protected function getItemData($item): array
    {
        $data = [];

        if ($item->getId() !== null) {
            $data['uid'] = $item->getId();
        }

        foreach ($this->fields as $field) {
            $method = $this->getItemMethod($item, $field, 'get');
            $data[$field] = $item->$method(); // @phpstan-ignore-line dynamic method call based on item schema
        }

        return $data;
    }

    public function getPid(): int
    {
        return $this->pid;
    }

    public function setPid(int $pid): void
    {
        $this->pid = $pid;
    }

    /**
     * @param ?array{page:int,itemsPerPage:int,sorting:array<string,string>} $navigation
     */
    protected function applyPagination(QueryBuilder $queryBuilder, ?array $navigation): void
    {
        if ($navigation !== null && $navigation['itemsPerPage'] > 0) {
            $queryBuilder->setMaxResults($navigation['itemsPerPage']);
            if ($navigation['page'] > 0) {
                $queryBuilder->setFirstResult($navigation['page']);
            }
        }
    }

    /**
     * @param ?array{page:int,itemsPerPage:int,sorting:array<string,string>} $navigation
     */
    protected function applySorting(QueryBuilder $queryBuilder, ?array $navigation): void
    {
        if ($navigation !== null && $navigation['sorting'] !== []) {
            foreach ($navigation['sorting'] as $field => $direction) {
                $queryBuilder->addOrderBy($field, $direction);
            }
        }
    }

    /**
     * @param ?array{page:int,itemsPerPage:int,sorting:array<string,string>} $navigation
     */
    protected function applyNavigation(QueryBuilder $queryBuilder, ?array $navigation): void
    {
        $this->applyPagination($queryBuilder, $navigation);
        $this->applySorting($queryBuilder, $navigation);
    }

    /**
     * @param Filters $filters
     */
    protected function applyFilters(QueryBuilder $queryBuilder, array $filters): void
    {
        $conditions = [];
        foreach ($filters as $field => $value) {
            if (is_array($value)) {
                if ($value !== []) {
                    $conditions[] = $queryBuilder->expr()->in($field, $value);
                }
            } else {
                if ($value !== '') {
                    $conditions[] = $queryBuilder->expr()->eq($field, $value);
                }
            }
        }

        if ($conditions !== []) {
            $queryBuilder->andWhere(...$conditions);
        }
    }

    /**
     * @param Filters $filters
     * @param ?array{page:int,itemsPerPage:int,sorting:array<string,string>} $navigation
     */
    protected function buildQuery(array $filters, ?array $navigation = null): QueryBuilder
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable($this->tableName);
        $queryBuilder->addSelect(...$this->fields);
        $queryBuilder->from($this->tableName);
        $queryBuilder->where($queryBuilder->expr()->eq('pid', $this->getPid()));
        $this->applyFilters($queryBuilder, $filters);
        $this->applyNavigation($queryBuilder, $navigation);

        return $queryBuilder;
    }

    /**
     * @param Filters $filters
     * @param ?array{page:int,itemsPerPage:int,sorting:array<string,string>} $navigation
     */
    protected function buildCountQuery(array $filters, ?array $navigation = null): QueryBuilder
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable($this->tableName);
        $queryBuilder->count('*');
        $queryBuilder->from($this->tableName);
        $queryBuilder->where($queryBuilder->expr()->eq('pid', $this->getPid()));
        $this->applyFilters($queryBuilder, $filters);
        $this->applyNavigation($queryBuilder, $navigation);

        return $queryBuilder;
    }

    public function fetchFiltered(array $filters, ?array $navigation = null): array
    {
        $queryBuilder = $this->buildQuery($filters, $navigation);

        $rows = $queryBuilder->executeQuery()->fetchAllAssociative();
        $result = [];
        foreach ($rows as $row) {
            $result[] = $this->create($row);
        }

        return $result;
    }

    public function countFiltered(array $filters): int
    {
        $queryBuilder = $this->buildCountQuery($filters);

        $count = $queryBuilder->executeQuery()->fetchOne();

        if ($count === false) {
            throw new DigitalMarketingFrameworkException(sprintf('Unable to calculate item count on table "%s"', $this->tableName));
        }

        return $count;
    }

    public function fetchById($id): ?Item
    {
        $result = $this->fetchFiltered(['uid' => $id], ['page' => 0, 'itemsPerPage' => 1, 'sorting' => []]);

        if ($result === []) {
            return null;
        }

        return reset($result);
    }

    public function countAll(): int
    {
        return $this->countFiltered([]);
    }

    public function fetchAll(?array $navigation = null): array
    {
        return $this->fetchFiltered([], $navigation);
    }

    public function add($item): void
    {
        $data = $this->getItemData($item);
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable($this->tableName);
        $queryBuilder->insert($this->tableName);
        foreach ($this->fields as $field) {
            $queryBuilder->setValue($field, $data[$field]);
        }
        $queryBuilder->setValue('pid', $this->getPid());
        $queryBuilder->executeStatement();
        $uid = (int) $queryBuilder->getConnection()->lastInsertId();
        $item->setId($uid);
    }

    public function update($item): void
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable($this->tableName);
        $queryBuilder->update($this->tableName);
        $data = $this->getItemData($item);
        foreach ($this->fields as $field) {
            $queryBuilder->setValue($field, $data[$field]);
        }
        $queryBuilder->setValue('pid', $this->getPid());
        $queryBuilder->executeStatement();
    }

    public function remove($item): void
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable($this->tableName);
        $queryBuilder->delete($this->tableName);
        $queryBuilder->where($queryBuilder->expr()->eq('uid', $item->getId()));
        $queryBuilder->executeStatement();
    }

    public static function getSchema(): ContainerSchema
    {
        return new ContainerSchema();
    }
}
