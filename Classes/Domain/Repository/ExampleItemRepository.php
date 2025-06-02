<?php

namespace DigitalMarketingFramework\Typo3\Core\Domain\Repository;

use DigitalMarketingFramework\Typo3\Core\Domain\Model\ExampleItem;
use DigitalMarketingFramework\Typo3\Core\Domain\Model\Item;

/**
 * @extends ItemStorageRepository<ExampleItem,array{name?:string,description?:string},array{name?:string}>
 */
class ExampleItemRepository extends ItemStorageRepository
{
    protected function getExtensionKey(): string
    {
        return 'dmf_core';
    }

    public function create(?array $data = null): Item
    {
        return new ExampleItem(
            $data['name'] ?? '',
            $data['description'] ?? ''
        );
    }

    public function fetchFiltered(array $filters, ?array $navigation = null): array
    {
        $query = $this->createQuery();

        if (isset($filters['name']) && $filters['name'] !== '') {
            $query->matching($query->equals('name', $filters['name']));
        }

        $this->applyNavigation($query, $navigation);

        return $query->execute()->toArray();
    }

    public function updateTrimDescription(): void
    {
        $allExampleItems = $this->fetchAll();
        foreach ($allExampleItems as $exampleItem) {
            $exampleItem->setDescription(trim($exampleItem->getDescription()));
            $this->update($exampleItem);
        }
    }
}
