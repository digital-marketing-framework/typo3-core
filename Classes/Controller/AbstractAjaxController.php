<?php

namespace DigitalMarketingFramework\Typo3\Core\Controller;

use DigitalMarketingFramework\Core\ConfigurationDocument\Controller\ConfigurationEditorControllerInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\Parser\ConfigurationDocumentParserInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class AbstractAjaxController
{
    public function __construct(
        protected ResponseFactoryInterface $responseFactory,
        protected ConfigurationDocumentParserInterface $parser,
        protected ConfigurationEditorControllerInterface $editorController,
    ) {
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
        return $this->jsonResponse($this->editorController->getSchemaDocumentAsArray());
    }

    public function defaultsAction(ServerRequestInterface $request): ResponseInterface
    {
        $schemaDocument = $this->editorController->getSchemaDocument();
        $defaults = $this->editorController->getDefaultConfiguration();
        $schemaDocument->preSaveDataTransform($defaults);

        return $this->jsonResponse($defaults);
    }

    public function mergeAction(ServerRequestInterface $request): ResponseInterface
    {
        $schemaDocument = $this->editorController->getSchemaDocument();
        $document = json_decode((string)$request->getBody(), true, 512, JSON_THROW_ON_ERROR)['document'] ?? '';
        $configuration = $this->parser->parseDocument($document);

        $mergedConfiguration = $this->editorController->mergeConfiguration($configuration);
        $mergedInheritedConfiguration = $this->editorController->mergeConfiguration($configuration, inheritedConfigurationOnly: true);

        $schemaDocument->preSaveDataTransform($mergedConfiguration);
        $schemaDocument->preSaveDataTransform($mergedInheritedConfiguration);

        return $this->jsonResponse([
            'configuration' => $mergedConfiguration,
            'inheritedConfiguration' => $mergedInheritedConfiguration,
        ]);
    }

    public function splitAction(ServerRequestInterface $request): ResponseInterface
    {
        $schemaDocument = $this->editorController->getSchemaDocument();
        $mergedConfiguration = json_decode((string)$request->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $splitConfiguration = $this->editorController->splitConfiguration($mergedConfiguration);
        $splitDocument = $this->parser->produceDocument($splitConfiguration, $schemaDocument);

        return $this->jsonResponse(['document' => $splitDocument]);
    }

    public function updateIncludesAction(ServerRequestInterface $request): ResponseInterface
    {
        $schemaDocument = $this->editorController->getSchemaDocument();
        $data = json_decode((string)$request->getBody(), true, 512, JSON_THROW_ON_ERROR);

        $mergedConfiguration = $this->editorController->processIncludesChange(
            $data['referenceData'],
            $data['newData']
        );

        $mergedInheritedConfiguration = $this->editorController->processIncludesChange(
            $data['referenceData'],
            $data['newData'],
            inheritedConfigurationOnly: true
        );

        $schemaDocument->preSaveDataTransform($mergedConfiguration);
        $schemaDocument->preSaveDataTransform($mergedInheritedConfiguration);

        return $this->jsonResponse([
            'configuration' => $mergedConfiguration,
            'inheritedConfiguration' => $mergedInheritedConfiguration,
        ]);
    }
}
