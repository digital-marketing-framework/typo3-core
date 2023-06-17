<?php

namespace DigitalMarketingFramework\Typo3\Core\Controller;

use Psr\Http\Message\ResponseInterface;

class BackendOverviewController extends AbstractBackendController
{
    public function showAction(): ResponseInterface
    {
        return $this->backendHtmlResponse();
    }
}
