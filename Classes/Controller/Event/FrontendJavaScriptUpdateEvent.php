<?php

namespace DigitalMarketingFramework\Typo3\Core\Controller\Event;

use DigitalMarketingFramework\Core\Registry\RegistryInterface;

class FrontendJavaScriptUpdateEvent
{
    /** @var array<string,array<string,array<string>>> */
    protected array $frontendScripts = [];

    public function processRegistry(RegistryInterface $registry): void
    {
        foreach ($registry->getFrontendScripts() as $type => $typeScripts) {
            foreach ($typeScripts as $package => $paths) {
                $scripts = $this->frontendScripts[$type][$package] ??= [];
                array_push($scripts, ...$paths);
                $this->frontendScripts[$type][$package] = array_unique($scripts);
            }
        }
    }

    /**
     * @return array<string,array<string,array<string>>>
     */
    public function getFrontendScripts(): array
    {
        return $this->frontendScripts;
    }
}
