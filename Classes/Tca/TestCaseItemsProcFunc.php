<?php

namespace DigitalMarketingFramework\Typo3\Core\Tca;

use DigitalMarketingFramework\Typo3\Core\Registry\RegistryCollection;

class TestCaseItemsProcFunc
{
    public function __construct(
        protected RegistryCollection $registryCollection,
    ) {
    }

    /**
     * @param array<string,mixed> $configuration
     */
    public function getAllTestCaseTypes(array &$configuration): void
    {
        $types = $this->registryCollection->getRegistry()->getAllTestCaseProcessorTypes();
        foreach ($types as $type) {
            $configuration['items'][] = [$type, $type];
        }
    }
}
