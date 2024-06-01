<?php

namespace DigitalMarketingFramework\Typo3\Core\GlobalConfiguration\Schema;

use DigitalMarketingFramework\Core\GlobalConfiguration\Schema\CoreGlobalConfigurationSchema as OriginalCoreGlobalConfigurationSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\IntegerSchema;

class CoreGlobalConfigurationSchema extends OriginalCoreGlobalConfigurationSchema
{
    /**
     * @var string
     */
    public const KEY_CONFIGURATION_STORAGE_PID = 'pid';

    /**
     * @var int
     */
    public const DEFAULT_CONFIGURATION_STORAGE_PID = 0;

    public function __construct()
    {
        parent::__construct();

        $pidSchema = new IntegerSchema(static::DEFAULT_CONFIGURATION_STORAGE_PID);
        $pidSchema->getRenderingDefinition()->setLabel('Storage PID');
        $this->apiSchema->addProperty(static::KEY_CONFIGURATION_STORAGE_PID, $pidSchema);
    }
}
