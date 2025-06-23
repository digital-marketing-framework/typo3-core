<?php

namespace DigitalMarketingFramework\Typo3\Core\TestCase\GlobalConfiguration\Settings;

use DigitalMarketingFramework\Core\GlobalConfiguration\Settings\GlobalSettings;
use DigitalMarketingFramework\Typo3\Core\GlobalConfiguration\Schema\CoreGlobalConfigurationSchema;

class TestCaseSettings extends GlobalSettings
{
    public function __construct()
    {
        parent::__construct('core', CoreGlobalConfigurationSchema::KEY_TESTS);
    }

    public function getPid(): int
    {
        return $this->get(CoreGlobalConfigurationSchema::KEY_TESTS_STORAGE_PID);
    }
}
