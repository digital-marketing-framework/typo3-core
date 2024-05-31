<?php

namespace DigitalMarketingFramework\Typo3\Core\Registry\EventListener;

use DigitalMarketingFramework\Core\InitializationInterface;
use DigitalMarketingFramework\Core\Registry\RegistryDomain;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Core\Registry\RegistryUpdateType;
use DigitalMarketingFramework\Typo3\Core\Registry\Event\CoreRegistryUpdateEvent;

abstract class AbstractCoreRegistryUpdateEventListener
{
    protected const TEMPLATE_PATH_PATTERN = 'EXT:%s/Resources/Private/TwigTemplates';

    protected const PARTIAL_PATH_PATTERN = 'EXT:%s/Resources/Private/TwigPartials';

    protected const CONFIGURATION_DOCUMENTS_PATH_PATTERN = 'EXT:%s/Resources/Private/ConfigurationDocuments';

    public function __construct(
        protected InitializationInterface $initialization
    ) {
    }

    protected function initGlobalConfiguration(RegistryInterface $registry): void
    {
        $this->initialization->initGlobalConfiguration(RegistryDomain::CORE, $registry);
    }

    protected function initServices(RegistryInterface $registry): void
    {
        $this->initialization->initServices(RegistryDomain::CORE, $registry);

        $extKey = $this->initialization->getPackageAlias();
        if ($extKey !== '') {
            $registry->getTemplateService()->addTemplateFolder(sprintf(static::TEMPLATE_PATH_PATTERN, $extKey));
            $registry->getTemplateService()->addPartialFolder(sprintf(static::PARTIAL_PATH_PATTERN, $extKey));
            $registry->addStaticConfigurationDocumentFolderIdentifier(sprintf(static::CONFIGURATION_DOCUMENTS_PATH_PATTERN, $extKey));
        }
    }

    protected function initPlugins(RegistryInterface $registry): void
    {
        $this->initialization->initPlugins(RegistryDomain::CORE, $registry);
    }

    public function __invoke(CoreRegistryUpdateEvent $event): void
    {
        $registry = $event->getRegistry();

        // always init meta data
        $this->initialization->initMetaData($registry);

        // init rest depending on update type
        $type = $event->getUpdateType();
        switch ($type) {
            case RegistryUpdateType::GLOBAL_CONFIGURATION:
                $this->initGlobalConfiguration($registry);
                break;
            case RegistryUpdateType::SERVICE:
                $this->initServices($registry);
                break;
            case RegistryUpdateType::PLUGIN:
                $this->initPlugins($registry);
                break;
        }
    }
}
