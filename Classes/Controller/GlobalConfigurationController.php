<?php

namespace DigitalMarketingFramework\Typo3\Core\Controller;

use DigitalMarketingFramework\Core\ConfigurationDocument\ConfigurationDocumentManagerInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\Parser\ConfigurationDocumentParserInterface;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationInterface;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\SchemaProcessorInterface;
use DigitalMarketingFramework\Typo3\Core\Registry\RegistryCollection;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Imaging\IconFactory;

class GlobalConfigurationController extends AbstractBackendController
{
    protected GlobalConfigurationInterface $globalConfiguration;

    protected ConfigurationDocumentParserInterface $parser;

    protected SchemaProcessorInterface $schemaProcessor;

    protected SchemaDocument $schemaDocument;

    public function __construct(
        ModuleTemplateFactory $moduleTemplateFactory,
        IconFactory $iconFactory,
        RegistryCollection $registryCollection,
    ) {
        parent::__construct($moduleTemplateFactory, $iconFactory);
        $registry = $registryCollection->getRegistryByClass(RegistryInterface::class);

        $this->schemaDocument = $registryCollection->getGlobalConfigurationSchemaDocument();
        $this->globalConfiguration = $registry->getGlobalConfiguration();
        $this->parser = $registry->getConfigurationDocumentParser();
        $this->schemaProcessor = $registry->getSchemaProcessor();
    }

    public function editAction(): ResponseInterface
    {
        $data = [];
        $properties = $this->schemaDocument->getMainSchema()->getProperties();
        foreach (array_keys($properties) as $key) {
            $data[$key] = $this->globalConfiguration->get($key) ?? [];
        }

        $this->schemaProcessor->convertValueTypes($this->schemaDocument, $data);
        $document = $this->parser->produceDocument($data, $this->schemaDocument);
        $this->view->assign('document', $document);

        return $this->backendHtmlResponse();
    }

    public function saveAction(string $document): ResponseInterface
    {
        $configuration = $this->parser->parseDocument($document);
        foreach ($configuration as $key => $value) {
            if ($key === ConfigurationDocumentManagerInterface::KEY_META_DATA) {
                continue;
            }

            $config = $this->globalConfiguration->get($key);
            foreach ($value as $configKey => $configValue) {
                $config[$configKey] = $configValue;
            }

            $this->globalConfiguration->set($key, $config);
        }

        return $this->redirectResponse(action: 'edit');
    }
}
