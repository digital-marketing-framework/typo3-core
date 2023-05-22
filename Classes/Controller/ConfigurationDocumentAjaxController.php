<?php

namespace DigitalMarketingFramework\Typo3\Core\Controller;

use DigitalMarketingFramework\Core\ConfigurationDocument\ConfigurationDocumentManagerInterface;
use DigitalMarketingFramework\Core\Utility\ConfigurationUtility;
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
        $configurationStack = $this->configurationDocumentManager->getConfigurationStackFromDocument($document);
        $configuration = ConfigurationUtility::mergeConfigurationStack($configurationStack);
        return $this->jsonResponse($configuration);
    }

    protected function splitConfiguration(array $mergedConfiguration): array
    {
        $configurationStack = $this->configurationDocumentManager->getConfigurationStackFromConfiguration($mergedConfiguration);
        array_pop($configurationStack);
        $parentConfiguration = ConfigurationUtility::mergeConfigurationStack($configurationStack);
        return ConfigurationUtility::splitConfiguration($parentConfiguration, $mergedConfiguration);
    }

    public function splitAction(ServerRequestInterface $request): ResponseInterface
    {
        $mergedConfiguration = json_decode((string)$request->getBody(), true);
        $splitConfiguration = $this->splitConfiguration($mergedConfiguration);
        $splitDocument = $this->configurationDocumentManager->getParser()->produceDocument($splitConfiguration);
        return $this->jsonResponse(['document' => $splitDocument]);
    }

    public function updateIncludesAction(ServerRequestInterface $request): ResponseInterface
    {
        $data = json_decode((string)$request->getBody(), true);
        $referenceData = $data['referenceData'];
        $newData = $data['newData'];

        $oldIncludes = $referenceData['includes'];
        $newIncludes = $newData['includes'];

        $mergedData = $newData;
        $mergedData['includes'] = $oldIncludes;
        $splitData = $this->splitConfiguration($mergedData);

        $splitData['includes'] = $newIncludes;
        $configurationStack = $this->configurationDocumentManager->getConfigurationStackFromConfiguration($splitData);
        $configuration = ConfigurationUtility::mergeConfigurationStack($configurationStack);

        return $this->jsonResponse($configuration);
    }
}
