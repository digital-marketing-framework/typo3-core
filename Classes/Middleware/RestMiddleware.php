<?php

namespace DigitalMarketingFramework\Typo3\Core\Middleware;

use DigitalMarketingFramework\Core\Api\Response\ApiResponseInterface;
use DigitalMarketingFramework\Core\Api\RouteResolver\EntryRouteResolverInterface;
use DigitalMarketingFramework\Typo3\Core\Registry\RegistryCollection;
use JsonException;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RestMiddleware implements MiddlewareInterface
{
    protected EntryRouteResolverInterface $routeResolver;

    public function __construct(
        protected StreamFactoryInterface $streamFactory,
        protected ResponseFactoryInterface $responseFactory,
        protected EventDispatcherInterface $eventDispatcher,
        protected RegistryCollection $registryCollection,
    ) {
        $this->routeResolver = $this->registryCollection->getApiEntryRouteResolver();
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

        $apiRequest = $this->routeResolver->buildRequest(
            $request->getQueryParams()['dmfResource'] ?? '',
            $request->getMethod(),
            $data
        );

        return $this->routeResolver->resolveRequest($apiRequest);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (array_key_exists('dmfResource', $request->getQueryParams()) && $this->routeResolver->enabled()) {
            $apiResponse = $this->processRequest($request);

            return $this->buildResponse($apiResponse);
        }

        return $handler->handle($request);
    }
}
