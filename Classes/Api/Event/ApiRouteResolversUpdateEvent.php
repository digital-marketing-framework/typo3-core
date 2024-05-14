<?php

namespace DigitalMarketingFramework\Typo3\Core\Api\Event;

use DigitalMarketingFramework\Core\Api\RouteResolver\EntryRouteResolver;
use DigitalMarketingFramework\Core\Api\RouteResolver\EntryRouteResolverInterface;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;

class ApiRouteResolversUpdateEvent
{
    public function __construct(
        protected EntryRouteResolverInterface $resolver
    ) {
    }

    public function processRegistry(RegistryInterface $registry): void
    {
        foreach ($registry->getApiRouteResolvers() as $domain => $resolver) {
            $this->resolver->registerResolver($domain, $resolver);
        }
    }

    public function getResolver(): EntryRouteResolverInterface
    {
        return $this->resolver;
    }

    public function setResolver(EntryRouteResolverInterface $resolver): void
    {
        $this->resolver = $resolver;
    }
}
