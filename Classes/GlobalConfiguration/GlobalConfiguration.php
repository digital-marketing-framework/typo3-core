<?php

namespace DigitalMarketingFramework\Typo3\Core\GlobalConfiguration;

use DigitalMarketingFramework\Core\GlobalConfiguration\DefaultGlobalConfiguration;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

class GlobalConfiguration extends DefaultGlobalConfiguration
{
    public function __construct(
        protected ExtensionConfiguration $extensionConfiguration,
    ) {
        parent::__construct();
    }

    public function get(string $key, mixed $default = null): mixed
    {
        try {
            $key = $this->packageAliases->resolveAlias($key);

            return $this->extensionConfiguration->get($key);
        } catch (ExtensionConfigurationExtensionNotConfiguredException|ExtensionConfigurationPathDoesNotExistException) {
            return parent::get($key, $default);
        }
    }

    public function set(string $key, mixed $value): void
    {
        $key = $this->packageAliases->resolveAlias($key);
        $this->extensionConfiguration->set($key, $value);
    }
}
