<?php

namespace DigitalMarketingFramework\Typo3\Core\Backend\UriRouteResolver;

class Typo3DefaultUriRouteResolver extends Typo3UriRouteResolver
{
    /**
     * @var int
     */
    public const WEIGHT = 100;

    protected function doResolve(string $route, array $arguments = []): ?string
    {
        $parameters = [
            'dmf' => [
                'route' => $route,
                'arguments' => $arguments,
            ],
        ];

        if (str_starts_with($route, 'page')) {
            return (string)$this->getTypo3UriBuilder()->buildUriFromRoute('digitalmarketingframework_admin', $parameters);
        }

        return (string)$this->getTypo3UriBuilder()->buildUriFromRoute('ajax_digitalmarketingframework_json', $parameters);
    }
}
