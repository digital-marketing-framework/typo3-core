<?php

namespace DigitalMarketingFramework\Typo3\Core\Middleware;

use DigitalMarketingFramework\Core\Api\Response\ApiResponseInterface;
use DigitalMarketingFramework\Core\Api\RouteResolver\EntryRouteResolverInterface;
use DigitalMarketingFramework\Typo3\Core\Registry\RegistryCollection;
use DigitalMarketingFramework\Typo3\Core\Utility\ApiUtility;
use JsonException;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class RestMiddleware implements MiddlewareInterface
{
    protected EntryRouteResolverInterface $routeResolver;

    public function __construct(
        protected StreamFactoryInterface $streamFactory,
        protected ResponseFactoryInterface $responseFactory,
        protected EventDispatcherInterface $eventDispatcher,
    ) {
    }

    protected function buildResponse(ApiResponseInterface $apiResponse): ResponseInterface
    {
        $message = $apiResponse->getStatusMessage();
        $code = $apiResponse->getStatusCode();

        return $this->responseFactory->createResponse($code, $message ?? '')
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withHeader('Cache-Control', 'no-store, must-revalidate')
            ->withBody($this->streamFactory->createStream($apiResponse->getContent()));
    }

    protected function processRequest(ServerRequestInterface $request): ApiResponseInterface
    {
        $body = (string)$request->getBody();
        try {
            $data = $body === '' ? null : json_decode($body, true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            $data = null;
        }

        $arguments = $request->getQueryParams();
        $resource = $arguments['dmfResource'] ?? '';
        unset($arguments['dmfResource']);

        $routeResolver = $this->getRouteResolver();
        $apiRequest = $routeResolver->buildRequest(
            $resource,
            $request->getMethod(),
            $arguments,
            $data
        );

        return $routeResolver->resolveRequest($apiRequest);
    }

    protected function getRouteResolver(): EntryRouteResolverInterface
    {
        if (!isset($this->routeResolver)) {
            $registryCollection = GeneralUtility::makeInstance(RegistryCollection::class);
            $this->routeResolver = $registryCollection->getApiEntryRouteResolver();
        }

        return $this->routeResolver;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (array_key_exists('dmfResource', $request->getQueryParams()) && ApiUtility::enabled()) {
            $apiResponse = $this->processRequest($request);

            return $this->buildResponse($apiResponse);
        }

        return $handler->handle($request);
    }
}
