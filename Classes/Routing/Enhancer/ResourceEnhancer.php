<?php

namespace DigitalMarketingFramework\Typo3\Core\Routing\Enhancer;

use DigitalMarketingFramework\Typo3\Core\Utility\ApiUtility;
use TYPO3\CMS\Core\Routing\Enhancer\AbstractEnhancer;
use TYPO3\CMS\Core\Routing\Enhancer\RoutingEnhancerInterface;
use TYPO3\CMS\Core\Routing\Route;
use TYPO3\CMS\Core\Routing\RouteCollection;

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

    /**
     * {@inheritdoc}
     */
    public function enhanceForMatching(RouteCollection $collection): void
    {
        if (ApiUtility::enabled()) {
            $basePath = ApiUtility::getBasePath();
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
}
