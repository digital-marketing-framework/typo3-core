<?php

namespace DigitalMarketingFramework\Typo3\Core\Backend;

use DigitalMarketingFramework\Core\Backend\AssetUriBuilderInterface;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;

class AssetUriBuilder implements AssetUriBuilderInterface
{
    public function __construct(
        protected RegistryInterface $registry,
    ) {
    }

    public function build(string $path): string
    {
        return '/' . $this->registry->getAssetService()->makeAssetPublic($path);
    }
}
