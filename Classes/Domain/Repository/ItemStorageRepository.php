<?php

namespace DigitalMarketingFramework\Typo3\Core\Domain\Repository;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Model\ItemInterface;
use DigitalMarketingFramework\Core\Storage\ItemStorage;
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

    /**
     * @param class-string<ItemClass> $itemClassName
     */
    public function __construct(
        protected ConnectionPool $connectionPool,
        string $itemClassName,
        protected string $tableName,
    ) {
        parent::__construct($itemClassName);
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
     * @param array{page?:int,itemsPerPage?:int}|array{limit?:int,offset?:int}|null $navigation
     */
    protected function applyPagination(QueryBuilder $queryBuilder, ?array $navigation): void
    {
        if (isset($navigation['limit']) || isset($navigation['offset'])) {
            $limit = $navigation['limit'] ?? 0;
            $offset = $navigation['offset'] ?? 0;
        } else {
            $limit = $navigation['itemsPerPage'] ?? 0;
            $offset = $limit * ($navigation['page'] ?? 0);
        }

        if ($limit > 0) {
            $queryBuilder->setMaxResults($limit);
        }

        if ($offset > 0) {
            $queryBuilder->setFirstResult($offset);
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
        } elseif ($value !== '') {
            return $queryBuilder->expr()->eq($name, $queryBuilder->createNamedParameter($value, $type ?? Connection::PARAM_STR));
        }

        return null;
    }

    /**
     * @param ?array<string,mixed> $filters
     */
    protected function applyFilters(QueryBuilder $queryBuilder, ?array $filters): void
    {
        if ($filters === null) {
            return;
        }

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
     * @param ?array<string,mixed> $filters
     * @param ?array{page:int,itemsPerPage:int,sorting:array<string,string>} $navigation
     */
    protected function buildQuery(?array $filters = null, ?array $navigation = null): QueryBuilder
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

    /**
     * @param array<array<string,mixed>> $rows
     *
     * @return array<ItemClass>
     */
    protected function createResults(array $rows)
    {
        $result = [];
        foreach ($rows as $row) {
            $result[] = $this->create($row);
        }

        return $result;
    }

    public function fetchFiltered(array $filters, ?array $navigation = null): array
    {
        $queryBuilder = $this->buildQuery($filters, $navigation);
        $rows = $queryBuilder->executeQuery()->fetchAllAssociative();

        return $this->createResults($rows);
    }

    public function fetchOneFiltered(array $filters, ?array $navigation = null)
    {
        $navigation ??= [];
        $navigation['limit'] = 1;

        $result = $this->fetchFiltered($filters, $navigation);

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
            throw new DigitalMarketingFrameworkException(sprintf('Unable to calculate item count on table "%s"', $this->tableName), 8261419171);
        }

        return $count;
    }

    public function fetchById(string|int $id): ?ItemInterface
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

        $id = (int)$queryBuilder->getConnection()->lastInsertId();
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
}
