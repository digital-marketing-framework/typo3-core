<?php

namespace DigitalMarketingFramework\Typo3\Core\Backend\UriRouteResolver;

abstract class Typo3EditRecordUriRouteResolver extends Typo3UriRouteResolver
{
    /**
     * @var int
     */
    public const WEIGHT = 0;

    abstract protected function getTableName(): string;

    protected function doResolve(string $route, array $arguments = []): ?string
    {
        $id = (string)($arguments['id'] ?? '');
        $returnUrl = $this->getReturnUrl($arguments);

        return $this->buildRecordEditUrl($this->getTableName(), $id, $returnUrl);
    }
}
