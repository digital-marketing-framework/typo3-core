<?php

namespace DigitalMarketingFramework\Typo3\Core\Controller;

use DigitalMarketingFramework\Core\ConfigurationDocument\ConfigurationDocumentManagerInterface;
use DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Event\ConfigurationDocumentMetaDataUpdateEvent;
use DigitalMarketingFramework\Typo3\Core\Registry\Registry;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;

class ConfigurationDocumentAjaxController
{
    protected ConfigurationDocumentManagerInterface $configurationDocumentManager;

    protected ?ConfigurationDocumentMetaDataUpdateEvent $configurationDocumentMetaData = null;

    public function __construct(
        protected ResponseFactoryInterface $responseFactory,
        protected EventDispatcher $eventDispatcher,
        Registry $registry,
    ) {
        $this->configurationDocumentManager = $registry->getConfigurationDocumentManager();
    }

    protected function jsonResponse(array $result): ResponseInterface
    {
        $response = $this->responseFactory->createResponse()
            ->withHeader('Content-Type', 'application/json; charset=utf-8');
        $response->getBody()->write(json_encode($result, JSON_PRETTY_PRINT|JSON_THROW_ON_ERROR));
        return $response;
    }

    protected function adjustIncludes(array &$configuration): void
    {
        // TODO having the sys:defaults injected for processing purposes is one thing
        //      however, this code also actually adds the include to the document, when using the editor app
        //      should it remove the include once the processing is done, before the configuration is sent to the client?
        $includes = $this->configurationDocumentManager->getIncludes($configuration);
        if (empty($includes)) {
            $this->configurationDocumentManager->setIncludes($configuration, ['SYS:defaults']);
        }
    }

    protected function getConfigurationDocumentMetaData(): ConfigurationDocumentMetaDataUpdateEvent
    {
        if ($this->configurationDocumentMetaData === null) {
            $this->configurationDocumentMetaData = new ConfigurationDocumentMetaDataUpdateEvent();
            $this->eventDispatcher->dispatch($this->configurationDocumentMetaData);
        }
        return $this->configurationDocumentMetaData;
    }

    public function schemaAction(ServerRequestInterface $request): ResponseInterface
    {
        return $this->jsonResponse($this->getConfigurationDocumentMetaData()->getConfigurationSchema());
    }

    public function defaultsAction(ServerRequestInterface $request): ResponseInterface
    {
        $schemaDocument = $this->getConfigurationDocumentMetaData()->getSchemaDocument();
        $defaults = $this->getConfigurationDocumentMetaData()->getDefaultConfiguration();
        $schemaDocument->preSaveDataTransform($defaults);
        return $this->jsonResponse($defaults);
    }

    public function mergeAction(ServerRequestInterface $request): ResponseInterface
    {
        $schemaDocument = $this->getConfigurationDocumentMetaData()->getSchemaDocument();
        $document = json_decode((string)$request->getBody(), true)['document'] ?? '';
        $configuration = $this->configurationDocumentManager->getParser()->parseDocument($document);
        $this->adjustIncludes($configuration);

        $mergedConfiguration = $this->configurationDocumentManager->mergeConfiguration($configuration);
        $mergedInheritedConfiguration = $this->configurationDocumentManager->mergeConfiguration($configuration, inheritedConfigurationOnly:true);

        $schemaDocument->preSaveDataTransform($mergedConfiguration);
        $schemaDocument->preSaveDataTransform($mergedInheritedConfiguration);
        return $this->jsonResponse([
            'configuration' => $mergedConfiguration,
            'inheritedConfiguration' => $mergedInheritedConfiguration,
        ]);
    }

    public function splitAction(ServerRequestInterface $request): ResponseInterface
    {
        $schemaDocument = $this->getConfigurationDocumentMetaData()->getSchemaDocument();
        $mergedConfiguration = json_decode((string)$request->getBody(), true);
        $this->adjustIncludes($mergedConfiguration);
        $splitConfiguration = $this->configurationDocumentManager->splitConfiguration($mergedConfiguration);
        $splitDocument = $this->configurationDocumentManager->getParser()->produceDocument($splitConfiguration, $schemaDocument);
        return $this->jsonResponse(['document' => $splitDocument]);
    }

    public function updateIncludesAction(ServerRequestInterface $request): ResponseInterface
    {
        $schemaDocument = $this->getConfigurationDocumentMetaData()->getSchemaDocument();
        $data = json_decode((string)$request->getBody(), true);
        $this->adjustIncludes($data['referenceData']);
        $this->adjustIncludes($data['newData']);

        $mergedConfiguration = $this->configurationDocumentManager->processIncludesChange(
            $data['referenceData'],
            $data['newData']
        );

        $mergedInheritedConfiguration = $this->configurationDocumentManager->processIncludesChange(
            $data['referenceData'],
            $data['newData'],
            inheritedConfigurationOnly:true
        );

        $schemaDocument->preSaveDataTransform($mergedConfiguration);
        $schemaDocument->preSaveDataTransform($mergedInheritedConfiguration);
        return $this->jsonResponse([
            'configuration' => $mergedConfiguration,
            'inheritedConfiguration' => $mergedInheritedConfiguration,
        ]);
    }
}
