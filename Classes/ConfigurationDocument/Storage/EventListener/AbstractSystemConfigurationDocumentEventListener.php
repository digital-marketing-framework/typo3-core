<?php

namespace DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Storage\EventListener;

use DigitalMarketingFramework\Core\ConfigurationDocument\ConfigurationDocumentManagerInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\Parser\ConfigurationDocumentParserInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\SchemaDocument;
use DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Event\ConfigurationDocumentMetaDataUpdateEvent;
use DigitalMarketingFramework\Typo3\Core\Registry\Registry;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;

abstract class AbstractSystemConfigurationDocumentEventListener extends AbstractStaticConfigurationDocumentEventListener
{
    protected ConfigurationDocumentParserInterface $parser;

    protected ?ConfigurationDocumentMetaDataUpdateEvent $configurationDocumentMetaData = null;

    public function __construct(
        protected EventDispatcher $eventDispatcher,
        Registry $registry,
    ) {
        $this->parser = $registry->getConfigurationDocumentParser();
    }

    protected function buildMetaData(string $documentName): array
    {
        return [
            ConfigurationDocumentManagerInterface::KEY_META_DATA => [
                ConfigurationDocumentManagerInterface::KEY_DOCUMENT_NAME => $documentName,
            ],
        ];
    }

    protected function getConfigurationDocumentMetaData(): ConfigurationDocumentMetaDataUpdateEvent
    {
        if (!$this->configurationDocumentMetaData instanceof ConfigurationDocumentMetaDataUpdateEvent) {
            $this->configurationDocumentMetaData = new ConfigurationDocumentMetaDataUpdateEvent();
            $this->eventDispatcher->dispatch($this->configurationDocumentMetaData);
        }

        return $this->configurationDocumentMetaData;
    }

    protected function getSchemaDocument(): SchemaDocument
    {
        return $this->getConfigurationDocumentMetaData()->getSchemaDocument();
    }

    protected function buildDocument(array $metaData, ?array $config = null): string
    {
        $metaDataOnly = $config === null;
        return $this->parser->produceDocument(
            $metaDataOnly ? $metaData : $metaData + $config,
            $metaDataOnly ? null : $this->getSchemaDocument()
        );

        return $this->parser->produceDocument($config, $this->getSchemaDocument());
    }
}
