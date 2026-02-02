<?php

namespace DigitalMarketingFramework\Typo3\Core\Backend\Controller\SectionController;

use DigitalMarketingFramework\Core\Backend\Controller\SectionController\GlobalSettingsSectionController as CoreGlobalSettingsSectionController;

/**
 * TYPO3-specific GlobalSettingsSectionController that normalizes configuration
 * data types before saving.
 *
 * TYPO3's ExtensionConfiguration writes all values as strings ('1' instead of true,
 * '42' instead of 42). This controller normalizes the data to match TYPO3's format,
 * preventing unnecessary file changes when saving unchanged values.
 */
class GlobalSettingsSectionController extends CoreGlobalSettingsSectionController
{
    protected function preSave(array &$configuration): void
    {
        foreach ($configuration as &$value) {
            if (is_array($value)) {
                $this->preSave($value);
            } elseif (is_bool($value)) {
                // TYPO3 stores booleans as '1' or '0'
                $value = $value ? '1' : '0';
            } elseif (is_int($value)) {
                // TYPO3 stores integers as strings
                $value = (string)$value;
            }
        }
    }
}
