<?php

namespace DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Storage\EventListener;

use DigitalMarketingFramework\Core\ConfigurationDocument\ConfigurationDocumentManagerInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\Parser\ConfigurationDocumentParserInterface;
use DigitalMarketingFramework\Core\Registry\RegistryCollection;
use DigitalMarketingFramework\Core\Registry\RegistryCollectionInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\SchemaProcessorInterface;
use DigitalMarketingFramework\Typo3\Core\Registry\Registry;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;

abstract class AbstractSystemConfigurationDocumentEventListener extends AbstractStaticConfigurationDocumentEventListener
{
    protected ConfigurationDocumentParserInterface $parser;

    protected SchemaProcessorInterface $schemaProcessor;

    protected RegistryCollectionInterface $registryCollection;

    public function __construct(
        protected EventDispatcher $eventDispatcher,
        Registry $registry,
    ) {
        $this->parser = $registry->getConfigurationDocumentParser();
        $this->schemaProcessor = $registry->getSchemaProcessor();
    }

    protected function getRegistryCollection(): RegistryCollectionInterface
    {
        if (!isset($this->registryCollection)) {
            $this->registryCollection = new RegistryCollection();
            $this->eventDispatcher->dispatch($this->registryCollection);
        }
        return $this->registryCollection;
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

    /**
     * @param array<string,mixed> $metaData
     * @param ?array<string,mixed> $config
     */
    protected function buildDocument(array $metaData, ?array $config = null): string
    {
        $metaDataOnly = $config === null;

        return $this->parser->produceDocument(
            $metaDataOnly ? $metaData : $metaData + $config,
            $metaDataOnly ? null : $this->getRegistryCollection()->getConfigurationSchemaDocument()
        );
    }
}
