<?php

namespace DigitalMarketingFramework\Typo3\Core\Api\GlobalConfiguration\Settings;

use DigitalMarketingFramework\Core\GlobalConfiguration\Settings\GlobalSettings;
use DigitalMarketingFramework\Typo3\Core\GlobalConfiguration\Schema\CoreGlobalConfigurationSchema;

class EndPointSettings extends GlobalSettings
{
    public function __construct()
    {
        parent::__construct('core', CoreGlobalConfigurationSchema::KEY_API);
    }

    public function getPid(): int
    {
        return $this->get(CoreGlobalConfigurationSchema::KEY_CONFIGURATION_STORAGE_PID);
    }
}
