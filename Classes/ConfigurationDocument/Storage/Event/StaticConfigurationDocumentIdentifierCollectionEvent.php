<?php

namespace DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Storage\Event;

class StaticConfigurationDocumentIdentifierCollectionEvent
{
    /** @var array<string> */
    protected array $identifiers = [];

    public function addIdentifier(string $identifier): void
    {
        $this->identifiers[] = $identifier;
    }

    /**
     * @param array<string> $identifiers
     */
    public function addIdentifiers(array $identifiers): void
    {
        array_push($this->identifiers, ...$identifiers);
    }

    /**
     * @return array<string> $identifiers
     */
    public function getIdentifiers(): array
    {
        return $this->identifiers;
    }

    /**
     * @param array<string> $identifiers
     */
    public function setIdentifiers(array $identifiers): void
    {
        $this->identifiers = $identifiers;
    }
}
