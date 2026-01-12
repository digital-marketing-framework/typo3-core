<?php

declare(strict_types=1);

use Mediatis\Typo3CodingStandards\Php\Typo3RectorSetup;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\ClassMethod\RemoveParentDelegatingConstructorRector;

return static function (RectorConfig $rectorConfig): void
{
    Typo3RectorSetup::setup($rectorConfig, __DIR__);

    // TEMPORARY WORKAROUND: Rector 2.3.0 bug - crashes when child constructor has
    // default value but parent doesn't (EditSectionController::$routes = ['edit']
    // vs SectionController::$routes with no default).
    // Fixed on main branch (PR #7782, Dec 30 2025) but not yet released.
    // TODO: Remove this skip once Rector > 2.3.0 is released with the fix.
    // See: tasks/development/016-remove-rector-rule-exclusion.md
    $rectorConfig->skip([
        RemoveParentDelegatingConstructorRector::class,
    ]);
};
