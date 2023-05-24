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

    public function schemaAction(ServerRequestInterface $request): ResponseInterface
    {
        $event = new ConfigurationDocumentMetaDataUpdateEvent();
        $this->eventDispatcher->dispatch($event);
        return $this->jsonResponse($event->getConfigurationSchema());
    }

    public function defaultsAction(ServerRequestInterface $request): ResponseInterface
    {
        $event = new ConfigurationDocumentMetaDataUpdateEvent();
        $this->eventDispatcher->dispatch($event);
        return $this->jsonResponse($event->getDefaultConfiguration());
    }

    public function mergeAction(ServerRequestInterface $request): ResponseInterface
    {
        $document = json_decode((string)$request->getBody(), true)['document'] ?? '';
        $configuration = $this->configurationDocumentManager->getParser()->parseDocument($document);
        return $this->jsonResponse([
            'configuration' => $this->configurationDocumentManager->mergeConfiguration($configuration),
            'inheritedConfiguration' => $this->configurationDocumentManager->mergeConfiguration($configuration, inheritedConfigurationOnly:true),
        ]);
    }

    public function splitAction(ServerRequestInterface $request): ResponseInterface
    {
        $mergedConfiguration = json_decode((string)$request->getBody(), true);
        $splitConfiguration = $this->configurationDocumentManager->splitConfiguration($mergedConfiguration);
        $splitDocument = $this->configurationDocumentManager->getParser()->produceDocument($splitConfiguration);
        return $this->jsonResponse(['document' => $splitDocument]);
    }

    public function updateIncludesAction(ServerRequestInterface $request): ResponseInterface
    {
        $data = json_decode((string)$request->getBody(), true);
        return $this->jsonResponse([
            'configuration' => $this->configurationDocumentManager->processIncludesChange(
                $data['referenceData'],
                $data['newData']
            ),
            'inheritedConfiguration' => $this->configurationDocumentManager->processIncludesChange(
                $data['referenceData'],
                $data['newData'],
                inheritedConfigurationOnly:true
            ),
        ]);
    }
}
