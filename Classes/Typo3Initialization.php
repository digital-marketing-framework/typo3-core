<?php

namespace DigitalMarketingFramework\Typo3\Core;

use DigitalMarketingFramework\Core\ConfigurationDocument\Migration\ConfigurationDocumentMigrationInterface;
use DigitalMarketingFramework\Core\GlobalConfiguration\Schema\GlobalConfigurationSchemaInterface;
use DigitalMarketingFramework\Core\Initialization;
use DigitalMarketingFramework\Core\InitializationInterface;
use DigitalMarketingFramework\Core\Plugin\PluginInterface;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;

class Typo3Initialization extends Initialization implements Typo3InitializationInterface
{
    /** @var string */
    protected const FRONTEND_SCRIPT_PATTERN = 'EXT:%s/Resources/Public/Scripts/%s';

    /** @var string */
    protected const CONFIGURATION_DOCUMENT_FOLDER_PATTERN = 'EXT:%s/Resources/Private/%s';

    /** @var string */
    protected const TEMPLATE_FOLDER_PATTERN = 'EXT:%s/Resources/Private/TwigTemplates/%s';

    /** @var string */
    protected const PARTIAL_FOLDER_PATTERN = 'EXT:%s/Resources/Private/TwigPartials/%s';

    /** @var string */
    protected const LAYOUT_FOLDER_PATTERN = 'EXT:%s/Resources/Private/TwigLayouts/%s';

    /** @var array<"core"|"distributor"|"collector",array<class-string<PluginInterface>,array<string|int,class-string<PluginInterface>>>> */
    protected const PLUGINS = [];

    /** @var array<class-string<ConfigurationDocumentMigrationInterface>> */
    protected const SCHEMA_MIGRATIONS = [];

    /** @var array<string> */
    protected const CONFIGURATION_EDITOR_SCRIPTS = [];

    /** @var array<string,array<string>> */
    protected const FRONTEND_SCRIPTS = [];

    /** @var string[] */
    protected const CONFIGURATION_DOCUMENT_FOLDERS = ['ConfigurationDocuments'];

    /** @var array<string,int> */
    protected const TEMPLATE_FOLDERS = ['Frontend' => 200];

    /** @var array<string,int> */
    protected const LAYOUT_FOLDERS = ['Frontend' => 200];

    /** @var array<string,int> */
    protected const PARTIAL_FOLDERS = ['Frontend' => 200];

    /** @var array<string,int> */
    protected const BACKEND_TEMPLATE_FOLDERS = ['Backend' => 200];

    /** @var array<string,int> */
    protected const BACKEND_LAYOUT_FOLDERS = ['Backend' => 200];

    /** @var array<string,int> */
    protected const BACKEND_PARTIAL_FOLDERS = ['Backend' => 200];

    public function __construct(
        protected ?InitializationInterface $inner = null,
        string $packageName = '',
        string $schemaVersion = SchemaDocument::INITIAL_VERSION,
        string $packageAlias = '',
        ?GlobalConfigurationSchemaInterface $globalConfigurationSchema = null,
    ) {
        parent::__construct($packageName, $schemaVersion, $packageAlias, $globalConfigurationSchema);
    }

    protected function getPathIdentifier(): string
    {
        return $this->getPackageAlias();
    }

    public function getPackageAlias(): string
    {
        $alias = parent::getPackageAlias();
        if ($alias !== '') {
            return $alias;
        }

        return $this->inner?->getPackageAlias() ?? '';
    }

    public function getFullPackageName(): string
    {
        if ($this->packageName !== '') {
            return parent::getFullPackageName();
        }

        return $this->inner?->getFullPackageName() ?? '';
    }

    public function getGlobalConfigurationSchema(): ?GlobalConfigurationSchemaInterface
    {
        return parent::getGlobalConfigurationSchema()
            ?? $this->inner?->getGlobalConfigurationSchema();
    }

    public function initPlugins(string $domain, RegistryInterface $registry): void
    {
        $this->inner?->initPlugins($domain, $registry);
        parent::initPlugins($domain, $registry);
    }

    public function initServices(string $domain, RegistryInterface $registry): void
    {
        $this->inner?->initServices($domain, $registry);
        parent::initServices($domain, $registry);
    }

    public function initGlobalConfiguration(string $domain, RegistryInterface $registry): void
    {
        $this->inner?->initGlobalConfiguration($domain, $registry);
        parent::initGlobalConfiguration($domain, $registry);
    }

    public function initMetaData(RegistryInterface $registry): void
    {
        $this->inner?->initMetaData($registry);
        parent::initMetaData($registry);
    }
}
