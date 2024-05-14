<?php

namespace DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Storage\EventListener;

class CoreSystemConfigurationDocumentEventListener extends AbstractSystemConfigurationDocumentEventListener
{
    /**
     * @var string
     */
    public const ID_DEFAULTS = 'SYS:defaults';

    /**
     * @var string
     */
    public const ID_RESET = 'SYS:reset';

    /**
     * @return array<string>
     */
    protected function getIdentifiers(): array
    {
        return [
            static::ID_DEFAULTS,
            static::ID_RESET,
        ];
    }

    /**
     * @return array<string,mixed>
     */
    protected function getDefaults(): array
    {
        $schemaDocument = $this->getRegistryCollection()->getConfigurationSchemaDocument();

        return $this->schemaProcessor->getDefaultValue($schemaDocument);
    }

    /**
     * @return array<string,mixed>
     */
    protected function getResetConfig(): array
    {
        $reset = [];
        $defaults = $this->getDefaults();
        foreach (array_keys($defaults) as $key) {
            $reset[$key] = null;
        }

        return $reset;
    }

    protected function getResetDocument(bool $metaDataOnly = false): string
    {
        $metaData = $this->buildMetaData('Reset');
        $config = $metaDataOnly ? null : $this->getResetConfig();

        return $this->buildDocument($metaData, $config);
    }

    protected function getDefaultsDocument(bool $metaDataOnly = false): string
    {
        $metaData = $this->buildMetaData('Defaults');
        $config = $metaDataOnly ? null : $this->getDefaults();

        return $this->buildDocument($metaData, $config);
    }

    protected function getDocument(string $documentIdentifier, bool $metaDataOnly = false): ?string
    {
        return match ($documentIdentifier) {
            static::ID_DEFAULTS => $this->getDefaultsDocument($metaDataOnly),
            static::ID_RESET => $this->getResetDocument($metaDataOnly),
            default => null,
        };
    }
}
