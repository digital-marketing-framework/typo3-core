<?php

namespace DigitalMarketingFramework\Typo3\Core\Domain\Model;

use DigitalMarketingFramework\Core\Model\ItemInterface;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * @implements ItemInterface<int>
 */
abstract class Item extends AbstractEntity implements ItemInterface
{
    public function getId(): int
    {
        return $this->uid;
    }
}
