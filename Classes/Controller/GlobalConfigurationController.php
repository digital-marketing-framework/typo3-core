<?php

namespace DigitalMarketingFramework\Typo3\Core\Controller;

use DigitalMarketingFramework\Core\ConfigurationDocument\ConfigurationDocumentManagerInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\Parser\ConfigurationDocumentParserInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\SchemaDocument;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationInterface;
use DigitalMarketingFramework\Typo3\Core\Registry\Registry;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Core\Imaging\IconFactory;

class GlobalConfigurationController extends AbstractBackendController
{
    protected ConfigurationDocumentParserInterface $parser;

    protected GlobalConfigurationInterface $globalConfiguration;

    protected SchemaDocument $schemaDocument;

    public function __construct(
        ModuleTemplateFactory $moduleTemplateFactory,
        IconFactory $iconFactory,
        Registry $registry,
    ) {
        parent::__construct($moduleTemplateFactory, $iconFactory);
        $this->parser = $registry->getConfigurationDocumentParser();
        $this->globalConfiguration = $registry->getGlobalConfiguration();
        $this->schemaDocument = $registry->getSchemaDocument(Registry::SCHEMA_KEY_GLOBAL_CONFIGURATION);
    }

    public function editAction(): ResponseInterface
    {
        $data = [];
        $properties = $this->schemaDocument->getMainSchema()->getProperties();
        foreach (array_keys($properties) as $key) {
            $data[$key] = $this->globalConfiguration->get($key) ?? [];
        }
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
            $this->globalConfiguration->set($key, $value);
        }
        return $this->redirectResponse(action: 'edit');
    }
}
