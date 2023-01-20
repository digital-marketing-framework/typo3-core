<?php

namespace DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Storage\Event;

class StaticConfigurationDocumentIdentifierCollectionEvent
{
    protected array $identifiers = [];

    public function addIdentifier(string $identifier): void
    {
        $this->identifiers[] = $identifier;
    }

    public function addIdentifiers(array $identifiers): void
    {
        array_push($this->identifiers, ...$identifiers);
    }

    public function getIdentifiers(): array
    {
        return $this->identifiers;
    }

    public function setIdentifiers(array $identifiers): void
    {
        $this->identifiers = $identifiers;
    }
}
