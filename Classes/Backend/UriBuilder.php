<?php

namespace DigitalMarketingFramework\Typo3\Core\Backend;

use DigitalMarketingFramework\Core\Backend\UriBuilderInterface;
use TYPO3\CMS\Backend\Routing\UriBuilder as Typo3UriBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class UriBuilder implements UriBuilderInterface
{
    protected ?Typo3UriBuilder $typo3UriBuilder = null;

    protected function getTypo3UriBuilder(): Typo3UriBuilder
    {
        if (!$this->typo3UriBuilder instanceof Typo3UriBuilder) {
            $this->typo3UriBuilder = GeneralUtility::makeInstance(Typo3UriBuilder::class);
        }

        return $this->typo3UriBuilder;
    }

    public function build(string $route, array $arguments = []): string
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
