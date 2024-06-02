<?php

namespace DigitalMarketingFramework\Typo3\Core\Controller;

use DigitalMarketingFramework\Typo3\Core\Registry\RegistryCollection;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class FrontendController extends ActionController
{
    public function __construct(
        protected RegistryCollection $registryCollection,
    ) {
    }

    public function javaScriptSettingsAction(): ResponseInterface
    {
        $this->view->assign('DMF', $this->registryCollection->getFrontendSettings());
        $this->view->assign('scripts', $this->registryCollection->getFrontendScripts());

        return $this->htmlResponse();
    }
}
