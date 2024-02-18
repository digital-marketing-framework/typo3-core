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

    public function __construct(
        protected EventDispatcher $eventDispatcher,
        protected Registry $registry,
    ) {
        $this->parser = $registry->getConfigurationDocumentParser();
    }

    /**
     * @return array{metaData:array{name:string}}
     */
    protected function buildMetaData(string $documentName): array
    {
        return [
            ConfigurationDocumentManagerInterface::KEY_META_DATA => [
                ConfigurationDocumentManagerInterface::KEY_DOCUMENT_NAME => $documentName,
            ],
        ];
    }

    protected function getSchemaDocument(): SchemaDocument
    {
        return $this->registry->getSchemaDocument(Registry::SCHEMA_KEY_CONFIGURATION_DOCUMENT);
    }

    /**
     * @param array<string,mixed> $metaData
     * @param ?array<string,mixed> $config
     */
    protected function buildDocument(array $metaData, ?array $config = null): string
    {
        $metaDataOnly = $config === null;

        return $this->parser->produceDocument(
            $metaDataOnly ? $metaData : $metaData + $config,
            $metaDataOnly ? null : $this->getSchemaDocument()
        );
    }
}
