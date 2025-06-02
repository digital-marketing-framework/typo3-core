<?php

namespace DigitalMarketingFramework\Typo3\Core\Domain\Repository;

use BadMethodCallException;
use DigitalMarketingFramework\Core\Storage\ItemStorageInterface;
use DigitalMarketingFramework\Typo3\Core\Domain\Model\Item;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * @template ItemClass of Item
 * @template ItemData of array<string,mixed>
 * @template Filters of array<string,mixed>
 *
 * @extends Repository<ItemClass>
 *
 * @implements ItemStorageInterface<ItemClass,int,ItemData,Filters>
 */
abstract class ItemStorageRepository extends Repository implements ItemStorageInterface
{
    protected int $pid;

    public function __construct(
        protected ExtensionConfiguration $extensionConfiguration,
    ) {
        parent::__construct();
    }

    public function initializeObject(): void
    {
        $querySettings = GeneralUtility::makeInstance(Typo3QuerySettings::class);
        $querySettings->setRespectStoragePage(true);
        $querySettings->setStoragePageIds([$this->getPid()]);
        $this->setDefaultQuerySettings($querySettings);
    }

    abstract protected function getExtensionKey(): string;

    abstract public function create(?array $data = null): Item;

    protected function getPid(): int
    {
        if (!isset($this->pid)) {
            try {
                $this->pid = $this->extensionConfiguration->get($this->getExtensionKey())['storage']['pid'] ?? 0;
            } catch (ExtensionConfigurationExtensionNotConfiguredException|ExtensionConfigurationPathDoesNotExistException) {
                $this->pid = 0;
            }
        }

        return $this->pid;
    }

    public function fetchFiltered(array $filters, ?array $navigation = null): array
    {
        throw new BadMethodCallException('Method fetchFiltered() not supported.', 3658702165);
    }

    public function countFiltered(array $filters): int
    {
        throw new BadMethodCallException('Method fetchFiltered() not supported.', 3658702276);
    }

    public function fetchById($id): ?Item
    {
        return $this->findByIdentifier($id);
    }

    /**
     * @param QueryInterface<ItemClass> $query
     * @param array{page:int,itemsPerPage:int,sorting:array<string,string>} $navigation
     */
    protected function applyPagination(QueryInterface $query, ?array $navigation): void
    {
        if ($navigation !== null && $navigation['itemsPerPage'] > 0) {
            $query->setLimit($navigation['itemsPerPage']);
            if ($navigation['page'] > 0) {
                $query->setOffset($navigation['itemsPerPage'] * $navigation['page']);
            }
        }
    }

    /**
     * @param QueryInterface<ItemClass> $query
     * @param array{page:int,itemsPerPage:int,sorting:array<string,string>} $navigation
     */
    protected function applySorting(QueryInterface $query, ?array $navigation): void
    {
        if ($navigation !== null && $navigation['sorting'] !== []) {
            // TODO take sorting into account
        }
    }

    /**
     * @param QueryInterface<ItemClass> $query
     * @param array{page:int,itemsPerPage:int,sorting:array<string,string>} $navigation
     */
    protected function applyNavigation(QueryInterface $query, ?array $navigation): void
    {
        $this->applyPagination($query, $navigation);
        $this->applySorting($query, $navigation);
    }

    public function countAll(): int
    {
        $this->createQuery()->execute()->count();
    }

    public function fetchAll(?array $navigation = null): array
    {
        $query = $this->createQuery();
        $this->applyNavigation($query, $navigation);

        return $query->execute()->toArray();
    }

    /**
     * @param object $object
     */
    public function add($object): void
    {
        $object->setPid($this->getPid());
        parent::add($object);
        $this->persistenceManager->persistAll();
    }

    /**
     * @param object $modifiedObject
     */
    public function update($modifiedObject): void
    {
        $modifiedObject->setPid($this->getPid());
        parent::update($modifiedObject);
        $this->persistenceManager->persistAll();
    }
}
