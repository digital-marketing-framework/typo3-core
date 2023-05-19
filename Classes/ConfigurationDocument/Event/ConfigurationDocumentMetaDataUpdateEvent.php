<?php

namespace DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Event;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\SchemaDocument;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;

class ConfigurationDocumentMetaDataUpdateEvent
{
    protected array $defaultConfiguration = [];
    protected SchemaDocument $configurationSchema;

    public function __construct()
    {
        $this->configurationSchema = new SchemaDocument();
    }

    public function processRegistry(RegistryInterface $registry): void
    {
        $registry->addDefaultConfiguration($this->defaultConfiguration);
        $registry->addConfigurationSchema($this->configurationSchema);
    }

    public function resetDefaultConfiguration(): void
    {
        $this->defaultConfiguration = [];
    }

    public function resetConfigurationSchema(): void
    {
        $this->configurationSchema = new SchemaDocument();
    }

    public function reset(): void
    {
        $this->resetDefaultConfiguration();
        $this->resetConfigurationSchema();
    }

    public function getDefaultConfiguration(): array
    {
        return $this->defaultConfiguration;
    }

    public function getConfigurationSchema(): array
    {
        return $this->configurationSchema->toArray();
    }

    public function toArray(): array
    {
        return [
            'defaults' => $this->getDefaultConfiguration(),
            'schema' => $this->getConfigurationSchema(),
        ];
    }
}
