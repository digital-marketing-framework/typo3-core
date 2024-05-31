<?php

namespace DigitalMarketingFramework\Typo3\Core\Middleware;

use DigitalMarketingFramework\Core\Api\Response\ApiResponseInterface;
use DigitalMarketingFramework\Core\Api\RouteResolver\EntryRouteResolver;
use DigitalMarketingFramework\Core\Api\RouteResolver\EntryRouteResolverInterface;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;
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

    protected RegistryInterface $registry;

    public function __construct(
        protected StreamFactoryInterface $streamFactory,
        protected ResponseFactoryInterface $responseFactory,
        protected EventDispatcherInterface $eventDispatcher,
        protected RegistryCollection $registryCollection,
    ) {
        $this->registry = $this->registryCollection->getRegistryByClass(RegistryInterface::class);
    }

    protected function getRouteResolver(): EntryRouteResolverInterface
    {
        if (!isset($this->routeResolver)) {
            $this->routeResolver = $this->registry->createObject(EntryRouteResolver::class);
            $this->registryCollection->addApiRouteResolvers($this->routeResolver);
        }

        return $this->routeResolver;
    }

    protected function buildResponse(ServerRequestInterface $request, ApiResponseInterface $apiResponse): ResponseInterface
    {
        $message = $apiResponse->getStatusMessage();
        $code = $apiResponse->getStatusCode();

        return $this->responseFactory->createResponse($code, $message ?? '')
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withHeader('Cache-Control', 'no-store, must-revalidate')
            ->withBody($this->streamFactory->createStream($apiResponse->getContent()));
    }

    protected function processRequest(EntryRouteResolverInterface $resolver, ServerRequestInterface $request): ApiResponseInterface
    {
        $body = (string)$request->getBody();
        try {
            $data = $body === '' ? null : json_decode($body, true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            $data = null;
        }

        $apiRequest = $resolver->buildRequest(
            $request->getQueryParams()['dmfResource'] ?? '',
            $request->getMethod(),
            $data
        );

        return $resolver->resolveRequest($apiRequest);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (array_key_exists('dmfResource', $request->getQueryParams())) {
            $resolver = $this->getRouteResolver();
            if ($resolver->enabled()) {
                $apiResponse = $this->processRequest($resolver, $request);

                return $this->buildResponse($request, $apiResponse);
            }
        }

        return $handler->handle($request);
    }
}
