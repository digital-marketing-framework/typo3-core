<?php

namespace DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Event;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\SchemaDocument;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;

class ConfigurationDocumentMetaDataUpdateEvent
{
    protected SchemaDocument $configurationSchema;
    protected SchemaDocument $globalConfigurationSchema;

    public function __construct()
    {
        $this->configurationSchema = new SchemaDocument();
        $this->globalConfigurationSchema = new SchemaDocument();
    }

    public function processRegistry(RegistryInterface $registry): void
    {
        $registry->addConfigurationSchema($this->configurationSchema);
        $registry->addGlobalConfigurationSchema($this->globalConfigurationSchema);
    }

    public function reset(): void
    {
        $this->configurationSchema = new SchemaDocument();
        $this->globalConfigurationSchema = new SchemaDocument();
    }

    public function getSchemaDocument(): SchemaDocument
    {
        return $this->configurationSchema;
    }

    public function getGlobalConfigurationSchemaDocument(): SchemaDocument
    {
        return $this->globalConfigurationSchema;
    }

    /**
     * @return array<string,mixed>
     */
    public function getDefaultConfiguration(): array
    {
        /** @var array<string,mixed> */
        return $this->configurationSchema->getDefaultValue();
    }

    /**
     * @return array<string,mixed>
     */
    public function getDefaultGlobalConfiguration(): array
    {
        return $this->globalConfigurationSchema->getDefaultValue();
    }

    /**
     * @return array{valueSets:array<string,array<string,string>>,types:array<string,array<string,mixed>>,schema:array<string,mixed>}
     */
    public function getConfigurationSchema(): array
    {
        return $this->configurationSchema->toArray();
    }

    /**
     * @return array{valueSets:array<string,array<string,string>>,types:array<string,array<string,mixed>>,schema:array<string,mixed>}
     */
    public function getGlobalConfigurationSchema(): array
    {
        return $this->globalConfigurationSchema->toArray();
    }
}
