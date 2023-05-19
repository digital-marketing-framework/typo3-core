<?php

namespace DigitalMarketingFramework\Typo3\Core\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Form\Controller\AbstractBackendController;

class BackendOverviewController extends AbstractBackendController
{
    public function showAction(): ResponseInterface
    {
        return $this->htmlResponse();
    }
}
