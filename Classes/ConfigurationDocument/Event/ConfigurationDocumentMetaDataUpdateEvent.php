<?php

namespace DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Event;

use DigitalMarketingFramework\Core\Registry\RegistryInterface;

class ConfigurationDocumentMetaDataUpdateEvent
{
    protected $defaultConfiguration = [];
    protected $configurationSchema = [];

    public function processRegistry(RegistryInterface $registry): void
    {
        $this->addDefaultConfiguration($registry->getDefaultConfiguration());
        $this->addConfigurationSchema($registry->getConfigurationSchema());
    }

    protected function merge(array &$target, array $source): void
    {
        foreach ($source as $key => $value) {
            if (isset($target[$key]) && is_array($value) && is_array($target[$key])) {
                $this->merge($target[$key], $value);
            } else {
                $target[$key] = $value;
            }
        }
    }

    public function addDefaultConfiguration(array $defaultConfiguration): void
    {
        $this->merge($this->defaultConfiguration, $defaultConfiguration);
    }

    public function addConfigurationSchema(array $configurationSchema): void
    {
        $this->merge($this->configurationSchema, $configurationSchema);
    }

    public function resetDefaultConfiguration(): void
    {
        $this->defaultConfiguration = [];
    }

    public function resetConfigurationSchema(): void
    {
        $this->configurationSchema = [];
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
        return $this->configurationSchema;
    }
}
