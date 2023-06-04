<?php

namespace DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Event;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\SchemaDocument;
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
        $registry->addConfigurationSchema($this->configurationSchema);
    }

    public function reset(): void
    {
        $this->configurationSchema = new SchemaDocument();
    }

    public function getSchemaDocument(): SchemaDocument
    {
        return $this->configurationSchema;
    }

    public function getDefaultConfiguration(): array
    {
        return $this->configurationSchema->getDefaultValue();
    }

    public function getConfigurationSchema(): array
    {
        return $this->configurationSchema->toArray();
    }
}
