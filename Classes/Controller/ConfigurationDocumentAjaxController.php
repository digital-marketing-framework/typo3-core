<?php

namespace DigitalMarketingFramework\Typo3\Core\Controller;

use DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Event\ConfigurationDocumentMetaDataUpdateEvent;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;

class ConfigurationDocumentAjaxController
{
    protected ResponseFactoryInterface $responseFactory;
    protected EventDispatcher $eventDispatcher;

    public function __construct(
        ResponseFactoryInterface $responseFactory,
        EventDispatcher $eventDispatcher,
    ) {
        $this->responseFactory = $responseFactory;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function metaDataAction(ServerRequestInterface $request): ResponseInterface
    {
        $event = new ConfigurationDocumentMetaDataUpdateEvent();
        $this->eventDispatcher->dispatch($event);
        $result = $event->toArray();

        $response = $this->responseFactory->createResponse()
            ->withHeader('Content-Type', 'application/json; charset=utf-8');
        $response->getBody()->write(json_encode($result, JSON_PRETTY_PRINT|JSON_THROW_ON_ERROR));
        return $response;
    }
}
