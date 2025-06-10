<?php

namespace DigitalMarketingFramework\Typo3\Core\Domain\Repository;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationInterface;
use DigitalMarketingFramework\Core\Model\Item;
use DigitalMarketingFramework\Core\Model\ItemInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\Storage\ItemStorageInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\ParameterType;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;

/**
 * @template ItemClass of Item
 *
 * @implements ItemStorageInterface<ItemClass,int>
 */
abstract class ItemStorageRepository implements ItemStorageInterface
{
    protected ?int $pid = null;

    /** @var array<string> */
    protected array $fields;

    protected ?GlobalConfigurationInterface $globalConfiguration = null;

    /**
     * @param class-string<ItemClass> $itemClassName
     */
    public function __construct(
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
     * @return array<string,mixed>
     */
    protected function getItemData($item): array
    {
        $data = [];


        foreach ($this->fields as $field) {
            $method = $this->getItemMethod($item, $field, 'get');
            $data[$field] = $item->$method(); // @phpstan-ignore-line dynamic method call based on item schema
        }

        return $data;
    }

    public function getGlobalConfiguration(): ?GlobalConfigurationInterface
    {
        return $this->globalConfiguration;
    }

    public function setGlobalConfiguration(GlobalConfigurationInterface $globalConfiguration): void
    {
        $this->globalConfiguration = $globalConfiguration;
    }

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

    /**
     * @param ?array{page:int,itemsPerPage:int} $navigation
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
     * @param ?array{sorting:array<string,string>} $navigation
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

    protected function getFilterCondition(QueryBuilder $queryBuilder, string $name, mixed $value, ParameterType|ArrayParameterType|null $type = null): ?string
    {
        if (is_array($value)) {
            if ($value !== []) {
                return $queryBuilder->expr()->in($name, $queryBuilder->createNamedParameter($value, $type ?? Connection::PARAM_STR_ARRAY));
            }
        } else {
            if ($value !== '') {
                return $queryBuilder->expr()->eq($name, $queryBuilder->createNamedParameter($value, $type ?? Connection::PARAM_STR));
            }
        }

        return null;
    }

    /**
     * @param array<string,mixed> $filters
     */
    protected function applyFilters(QueryBuilder $queryBuilder, array $filters): void
    {
        $conditions = [];
        foreach ($filters as $field => $value) {
            $condition = $this->getFilterCondition($queryBuilder, $field, $value);
            if ($condition !== null) {
                $conditions[] = $condition;
            }
        }

        if ($conditions !== []) {
            $queryBuilder->andWhere(...$conditions);
        }
    }

    /**
     * @param array<string,mixed> $filters
     * @param ?array{page:int,itemsPerPage:int,sorting:array<string,string>} $navigation
     */
    protected function buildQuery(array $filters, ?array $navigation = null): QueryBuilder
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable($this->tableName);
        $queryBuilder->addSelect('uid', ...$this->fields);
        $queryBuilder->from($this->tableName);
        $queryBuilder->where($queryBuilder->expr()->eq('pid', $this->getPid()));
        $this->applyFilters($queryBuilder, $filters);
        $this->applyNavigation($queryBuilder, $navigation);

        return $queryBuilder;
    }

    /**
     * @param array<string,mixed> $filters
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

    public function fetchOneFiltered(array $filters)
    {
        $result = $this->fetchFiltered($filters);

        if ($result === []) {
            return null;
        }

        return reset($result);
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

    public function fetchByIdList(array $ids): array
    {
        return $this->fetchFiltered(['uid' => $ids]);
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
        $data['pid'] = $this->getPid();
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable($this->tableName);
        $queryBuilder->insert($this->tableName);
        $queryBuilder->values($data);
        $queryBuilder->executeStatement();
        $id = (int) $queryBuilder->getConnection()->lastInsertId();
        $item->setId($id);
    }

    public function update($item): void
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable($this->tableName);
        $queryBuilder->update($this->tableName);
        $queryBuilder->where($queryBuilder->expr()->eq('uid', $item->getId()));
        $data = $this->getItemData($item);
        foreach ($this->fields as $field) {
            $queryBuilder->set($field, $data[$field]);
        }
        $queryBuilder->set('pid', $this->getPid());
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
