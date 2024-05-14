<?php

namespace DigitalMarketingFramework\Typo3\Core\Controller;

use DigitalMarketingFramework\Core\Registry\RegistryCollection;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class FrontendController extends ActionController
{
    public function javaScriptSettingsAction(): ResponseInterface
    {
        $registryCollection = new RegistryCollection();
        $this->eventDispatcher->dispatch($registryCollection);

        $this->view->assign('DMF', $registryCollection->getFrontendSettings());
        $this->view->assign('scripts', $registryCollection->getFrontendScripts());

        return $this->htmlResponse();
    }
}
