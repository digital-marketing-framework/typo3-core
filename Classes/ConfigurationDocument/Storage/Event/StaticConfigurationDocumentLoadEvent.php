<?php

namespace DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Storage\Event;

class StaticConfigurationDocumentLoadEvent
{
    protected string $document = '';
    protected bool $loaded = false;

    public function __construct(
        protected string $identifier,
    ) {
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function setLoadedDocument(string $document): void
    {
        $this->loaded = true;
        $this->document = $document;
    }

    public function getDocument(): string
    {
        return $this->document;
    }

    public function isLoaded(): bool
    {
        return $this->loaded;
    }
}
