<?php

namespace DigitalMarketingFramework\Typo3\Core\Context;

use DigitalMarketingFramework\Core\Context\RequestContext;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class Typo3RequestContext extends RequestContext
{
    protected ServerRequestInterface $serverRequest;

    public function getServerRequest(): ServerRequestInterface
    {
        if (!isset($this->serverRequest)) {
            $this->serverRequest = $GLOBALS['TYPO3_REQUEST'];
        }

        return $this->serverRequest;
    }

    public function setServerRequest(ServerRequestInterface $serverRequest): void
    {
        $this->serverRequest = $serverRequest;
    }

    public function getRequestVariable(string $name): string
    {
        return GeneralUtility::getIndpEnv($name);
    }

    public function getRequestArgument(string $name): mixed
    {
        return $this->getServerRequest()->getQueryParams()[$name] ?? null;
    }

    public function getRequestArguments(): array
    {
        return $this->getServerRequest()->getQueryParams();
    }
}
