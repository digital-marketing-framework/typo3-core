<?php

namespace DigitalMarketingFramework\Typo3\Core\Routing\Enhancer;

use DigitalMarketingFramework\Core\Api\RouteResolver\EntryRouteResolverInterface;
use DigitalMarketingFramework\Core\Api\RouteResolver\RouteResolverInterface;
use DigitalMarketingFramework\Typo3\Core\Registry\RegistryCollection;
use TYPO3\CMS\Core\Routing\Enhancer\AbstractEnhancer;
use TYPO3\CMS\Core\Routing\Enhancer\RoutingEnhancerInterface;
use TYPO3\CMS\Core\Routing\Route;
use TYPO3\CMS\Core\Routing\RouteCollection;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * routeEnhancers:
 *   DMF:
 *     type: DmfResourceEnhancer
 */
class ResourceEnhancer extends AbstractEnhancer implements RoutingEnhancerInterface
{
    /**
     * @var string
     */
    public const ENHANCER_NAME = 'DmfResourceEnhancer';

    protected ?EntryRouteResolverInterface $entryRouteResolver = null;

    public function getEntryRouteResolver(): ?EntryRouteResolverInterface
    {
        if (!$this->entryRouteResolver instanceof EntryRouteResolverInterface) {
            $registryCollection = GeneralUtility::makeInstance(RegistryCollection::class);
            $this->entryRouteResolver = $registryCollection->getApiEntryRouteResolver();
        }

        return $this->entryRouteResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function enhanceForMatching(RouteCollection $collection): void
    {
        $entryRouteResolver = $this->getEntryRouteResolver();
        $enabled = $entryRouteResolver->enabled();
        if ($enabled) {
            $basePath = $entryRouteResolver->getBasePath();
            /** @var Route $variant */
            $variant = clone $collection->get('default');
            $variant->setPath($basePath . '/{dmfResource?}');
            $variant->setRequirement('dmfResource', '.*');
            $collection->add('enhancer_' . $basePath . spl_object_hash($variant), $variant);
        }
    }

    /**
     * @param array<mixed> $parameters
     */
    public function enhanceForGeneration(RouteCollection $collection, array $parameters): void
    {
    }

    protected function getBasePath(): string
    {
        return $this->getEntryRouteResolver()->getBasePath();
    }
}
