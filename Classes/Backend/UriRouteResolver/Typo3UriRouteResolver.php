<?php

namespace DigitalMarketingFramework\Typo3\Core\Backend\UriRouteResolver;

use DigitalMarketingFramework\Core\Backend\UriRouteResolver\UriRouteResolver;
use TYPO3\CMS\Backend\Routing\UriBuilder as Typo3UriBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

abstract class Typo3UriRouteResolver extends UriRouteResolver
{
    protected ?Typo3UriBuilder $typo3UriBuilder = null;

    protected function getTypo3UriBuilder(): Typo3UriBuilder
    {
        if (!$this->typo3UriBuilder instanceof Typo3UriBuilder) {
            $this->typo3UriBuilder = GeneralUtility::makeInstance(Typo3UriBuilder::class);
        }

        return $this->typo3UriBuilder;
    }

    protected function buildRecordEditUrl(string $table, int|string $id, string $returnUrl = ''): string
    {
        $parameters = [
            'edit' => [$table => [$id => 'edit']],
        ];

        if ($returnUrl !== '') {
            $parameters['returnUrl'] = $returnUrl;
        }

        return (string)$this->getTypo3UriBuilder()->buildUriFromRoute('record_edit', $parameters);
    }
}
