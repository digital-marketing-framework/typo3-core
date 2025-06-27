<?php

namespace DigitalMarketingFramework\Typo3\Core\GlobalConfiguration;

use DigitalMarketingFramework\Core\GlobalConfiguration\DefaultGlobalConfiguration;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

class GlobalConfiguration extends DefaultGlobalConfiguration
{
    public function __construct(
        RegistryInterface $registry,
        protected ExtensionConfiguration $extensionConfiguration,
    ) {
        parent::__construct($registry);
    }

    public function get(string $key, mixed $default = null, bool $resolvePlaceholders = true): mixed
    {
        try {
            $key = $this->packageAliases->resolveAlias($key);

            $value = $this->extensionConfiguration->get($key);
        } catch (ExtensionConfigurationExtensionNotConfiguredException|ExtensionConfigurationPathDoesNotExistException) {
            $value = parent::get($key, $default, false);
        }

        if ($resolvePlaceholders) {
            $value = $this->registry->getEnvironmentService()->insertEnvironmentVariables($value);
        }

        return $value;
    }

    public function set(string $key, mixed $value): void
    {
        $key = $this->packageAliases->resolveAlias($key);
        $this->extensionConfiguration->set($key, $value);
    }
}
