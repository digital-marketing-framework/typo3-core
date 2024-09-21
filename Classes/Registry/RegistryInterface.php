<?php

namespace DigitalMarketingFramework\Typo3\Core\Registry;

use TYPO3\CMS\Core\SingletonInterface;

interface RegistryInterface extends SingletonInterface
{
    public function init(): void;
}
