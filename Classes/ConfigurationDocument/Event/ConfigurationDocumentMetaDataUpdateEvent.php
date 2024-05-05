<?php

namespace DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Event;

use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;

class ConfigurationDocumentMetaDataUpdateEvent
{
    protected SchemaDocument $configurationSchema;

    public function __construct()
    {
        $this->configurationSchema = new SchemaDocument();
    }

    public function processRegistry(RegistryInterface $registry): void
    {
        $registry->addConfigurationSchemaDocument($this->configurationSchema);
    }

    public function reset(): void
    {
        $this->configurationSchema = new SchemaDocument();
    }

    public function getSchemaDocument(): SchemaDocument
    {
        return $this->configurationSchema;
    }

    /**
     * @return array{valueSets:array<string,array<string,string>>,types:array<string,array<string,mixed>>,schema:array<string,mixed>}
     */
    public function getConfigurationSchema(): array
    {
        return $this->configurationSchema->toArray();
    }
}
