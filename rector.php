<?php

declare(strict_types=1);

use Mediatis\Typo3CodingStandards\Php\Typo3RectorSetup;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\ClassMethod\RemoveParentDelegatingConstructorRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUselessReturnTagRector;
use Rector\Php74\Rector\LNumber\AddLiteralSeparatorToNumberRector;
use Rector\Php81\Rector\ClassConst\FinalizePublicClassConstantRector;
use Ssch\TYPO3Rector\CodeQuality\General\GeneralUtilityMakeInstanceToConstructorPropertyRector;

return static function (RectorConfig $rectorConfig): void
{
    Typo3RectorSetup::setup($rectorConfig, __DIR__);

    $skip = [
        // TEMPORARY WORKAROUND: Rector 2.3.0 bug - crashes when child constructor has
        // default value but parent doesn't (EditSectionController::$routes = ['edit']
        // vs SectionController::$routes with no default).
        // Fixed on main branch (PR #7782, Dec 30 2025) but not yet released.
        // TODO: Remove this skip once Rector > 2.3.0 is released with the fix.
        // See: tasks/development/016-remove-rector-rule-exclusion.md
        RemoveParentDelegatingConstructorRector::class,
    ];

    // Version-specific rule exclusions.
    // Use FinalizePublicClassConstantRector as version indicator - it exists only in older rector.
    // (InstalledVersions::getVersion() returns null inside rector's config loading context)
    $isOldRector = class_exists(FinalizePublicClassConstantRector::class);
    if ($isOldRector) {
        // Rules that exist in older rector but were deprecated/removed in newer versions.
        $skip = [
            ...$skip,
            // Skip: We don't want to force constants to be final
            FinalizePublicClassConstantRector::class,
            // Skip: Misinterprets Drupal/TYPO3 multi-line PHPDoc format where
            // @return descriptions are on indented continuation lines
            RemoveUselessReturnTagRector::class,
            // Skip: Exception codes are unix timestamps, underscores don't improve readability
            AddLiteralSeparatorToNumberRector::class,
        ];
    } else {
        // Rules that exist only in newer rector (TYPO3 13+).
        $skip = [
            ...$skip,
            // Skip: Anyrel's registry pattern requires GeneralUtility::makeInstance() for lazy loading
            // of CMS-specific services that may not be available during early initialization.
            GeneralUtilityMakeInstanceToConstructorPropertyRector::class,
        ];
    }

    $rectorConfig->skip($skip);
};
