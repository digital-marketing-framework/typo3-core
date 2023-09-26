<?php

namespace DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Storage\EventListener;

use DigitalMarketingFramework\Core\ConfigurationDocument\ConfigurationDocumentManagerInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\Parser\ConfigurationDocumentParserInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\SchemaDocument;
use DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Event\ConfigurationDocumentMetaDataUpdateEvent;
use DigitalMarketingFramework\Typo3\Core\Registry\Registry;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;

class CoreSystemConfigurationDocumentEventListener extends AbstractStaticConfigurationDocumentEventListener
{
    public const ID_DEFAULTS = 'SYS:defaults';
    public const ID_RESET = 'SYS:reset';

    protected ConfigurationDocumentParserInterface $parser;

    protected ?ConfigurationDocumentMetaDataUpdateEvent $configurationDocumentMetaData = null;

    public function __construct(
        protected EventDispatcher $eventDispatcher,
        Registry $registry,
    ) {
        $this->parser = $registry->getConfigurationDocumentParser();
    }

    protected function getIdentifiers(): array
    {
        return [
            static::ID_DEFAULTS,
            static::ID_RESET,
        ];
    }

    protected function getConfigurationDocumentMetaData(): ConfigurationDocumentMetaDataUpdateEvent
    {
        if ($this->configurationDocumentMetaData === null) {
            $this->configurationDocumentMetaData = new ConfigurationDocumentMetaDataUpdateEvent();
            $this->eventDispatcher->dispatch($this->configurationDocumentMetaData);
        }

        return $this->configurationDocumentMetaData;
    }

    protected function getSchemaDocument(): SchemaDocument
    {
        return $this->getConfigurationDocumentMetaData()->getSchemaDocument();
    }

    protected function getDefaults(): array
    {
        return $this->getConfigurationDocumentMetaData()->getDefaultConfiguration();
    }

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
        $metaData = [
            ConfigurationDocumentManagerInterface::KEY_META_DATA => [
                ConfigurationDocumentManagerInterface::KEY_DOCUMENT_NAME => 'Reset',
            ],
        ];
        $config = $metaDataOnly ? [] : $this->getResetConfig();

        return $this->parser->produceDocument(
            $metaData + $config,
            $metaDataOnly ? null : $this->getSchemaDocument()
        );
    }

    protected function getDefaultsDocument(bool $metaDataOnly = false): string
    {
        $metaData = [
            ConfigurationDocumentManagerInterface::KEY_META_DATA => [
                ConfigurationDocumentManagerInterface::KEY_DOCUMENT_NAME => 'Defaults',
            ],
        ];
        $config = $metaDataOnly ? [] : $this->getDefaults();

        return $this->parser->produceDocument(
            $metaData + $config,
            $metaDataOnly ? null : $this->getSchemaDocument()
        );
    }

    protected function getDocument(string $documentIdentifier, bool $metaDataOnly = false): ?string
    {
        switch ($documentIdentifier) {
            case static::ID_DEFAULTS:
                return $this->getDefaultsDocument($metaDataOnly);
            case static::ID_RESET:
                return $this->getResetDocument($metaDataOnly);
            default:
                return null;
        }
    }
}
