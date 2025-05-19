<?php

namespace DigitalMarketingFramework\Typo3\Core\Backend;

use DigitalMarketingFramework\Core\Backend\UriBuilderInterface;
use TYPO3\CMS\Backend\Routing\UriBuilder as Typo3UriBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class UriBuilder implements UriBuilderInterface
{
    public function build(string $route, array $arguments = []): string
    {
        $uriBuilder = GeneralUtility::makeInstance(Typo3UriBuilder::class);
        $parameters = [
            'dmf' => [
                'route' => $route,
                'arguments' => $arguments,
            ],
        ];
        if (str_starts_with($route, 'page')) {
            return (string)$uriBuilder->buildUriFromRoute('digitalmarketingframework_admin', $parameters);
        }

        return (string)$uriBuilder->buildUriFromRoute('ajax_digitalmarketingframework_json', $parameters);
    }
}
