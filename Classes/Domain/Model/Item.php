<?php

namespace DigitalMarketingFramework\Typo3\Core\Domain\Model;

use DigitalMarketingFramework\Core\Model\ItemInterface;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * @implements ItemInterface<int>
 */
abstract class Item implements ItemInterface
{
    protected ?int $uid = null;

    public function getId(): ?int
    {
        return $this->uid;
    }

    public function setId($id): void
    {
        $this->uid = $id;
    }
}
