<?php

namespace DigitalMarketingFramework\Typo3\Core\Controller\Event;

use DigitalMarketingFramework\Core\Registry\RegistryCollectionInterface;
use TYPO3\CMS\Extbase\Mvc\RequestInterface;

class FrontendSettingsUpdateEvent
{
    public function __construct(
        protected RequestInterface $request,
        protected RegistryCollectionInterface $registryCollection,
    ) {
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    public function getRegistryCollection(): RegistryCollectionInterface
    {
        return $this->registryCollection;
    }
}
