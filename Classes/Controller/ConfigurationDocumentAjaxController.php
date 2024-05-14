<?php

namespace DigitalMarketingFramework\Typo3\Core\Controller;

use DigitalMarketingFramework\Core\ConfigurationDocument\ConfigurationDocumentManagerInterface;
use DigitalMarketingFramework\Core\Registry\RegistryCollection;
use DigitalMarketingFramework\Core\Registry\RegistryCollectionInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\SchemaProcessorInterface;
use DigitalMarketingFramework\Typo3\Core\Registry\Registry;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;

class ConfigurationDocumentAjaxController
{
    protected ConfigurationDocumentManagerInterface $configurationDocumentManager;

    protected SchemaProcessorInterface $schemaProcessor;

    protected RegistryCollectionInterface $registryCollection;

    public function __construct(
        protected ResponseFactoryInterface $responseFactory,
        protected EventDispatcher $eventDispatcher,
        Registry $registry,
    ) {
        $this->configurationDocumentManager = $registry->getConfigurationDocumentManager();
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
     * @param array<mixed> $result
     */
    protected function jsonResponse(array $result): ResponseInterface
    {
        $response = $this->responseFactory->createResponse()
            ->withHeader('Content-Type', 'application/json; charset=utf-8');
        $response->getBody()->write(json_encode($result, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));

        return $response;
    }

    public function schemaAction(ServerRequestInterface $request): ResponseInterface
    {
        return $this->jsonResponse($this->getRegistryCollection()->getConfigurationSchemaDocument()->toArray());
    }

    public function defaultsAction(ServerRequestInterface $request): ResponseInterface
    {
        $schemaDocument = $this->getRegistryCollection()->getConfigurationSchemaDocument();
        $defaults = $this->schemaProcessor->getDefaultValue($schemaDocument);
        $this->schemaProcessor->preSaveDataTransform($schemaDocument, $defaults);

        return $this->jsonResponse($defaults);
    }

    public function mergeAction(ServerRequestInterface $request): ResponseInterface
    {
        $schemaDocument = $this->getRegistryCollection()->getConfigurationSchemaDocument();
        $document = json_decode((string)$request->getBody(), associative: true, flags: JSON_THROW_ON_ERROR)['document'] ?? '';
        $configuration = $this->configurationDocumentManager->getParser()->parseDocument($document);

        $mergedConfiguration = $this->configurationDocumentManager->mergeConfiguration($configuration);
        $mergedInheritedConfiguration = $this->configurationDocumentManager->mergeConfiguration($configuration, inheritedConfigurationOnly: true);

        $this->schemaProcessor->preSaveDataTransform($schemaDocument, $mergedConfiguration);
        $this->schemaProcessor->preSaveDataTransform($schemaDocument, $mergedInheritedConfiguration);

        return $this->jsonResponse([
            'configuration' => $mergedConfiguration,
            'inheritedConfiguration' => $mergedInheritedConfiguration,
        ]);
    }

    public function splitAction(ServerRequestInterface $request): ResponseInterface
    {
        $schemaDocument = $this->getRegistryCollection()->getConfigurationSchemaDocument();
        $mergedConfiguration = json_decode((string)$request->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $splitConfiguration = $this->configurationDocumentManager->splitConfiguration($mergedConfiguration);
        $splitDocument = $this->configurationDocumentManager->getParser()->produceDocument($splitConfiguration, $schemaDocument);

        return $this->jsonResponse(['document' => $splitDocument]);
    }

    public function updateIncludesAction(ServerRequestInterface $request): ResponseInterface
    {
        $schemaDocument = $this->getRegistryCollection()->getConfigurationSchemaDocument();
        $data = json_decode((string)$request->getBody(), true, 512, JSON_THROW_ON_ERROR);

        $mergedConfiguration = $this->configurationDocumentManager->processIncludesChange(
            $data['referenceData'],
            $data['newData']
        );

        $mergedInheritedConfiguration = $this->configurationDocumentManager->processIncludesChange(
            $data['referenceData'],
            $data['newData'],
            inheritedConfigurationOnly: true
        );

        $this->schemaProcessor->preSaveDataTransform($schemaDocument, $mergedConfiguration);
        $this->schemaProcessor->preSaveDataTransform($schemaDocument, $mergedInheritedConfiguration);

        return $this->jsonResponse([
            'configuration' => $mergedConfiguration,
            'inheritedConfiguration' => $mergedInheritedConfiguration,
        ]);
    }
}
