<?php

declare(strict_types=1);

use Mediatis\Typo3CodingStandards\Php\Typo3RectorSetup;
use Rector\Config\RectorConfig;
use Rector\ValueObject\PhpVersion;

return static function (RectorConfig $rectorConfig): void
{
    Typo3RectorSetup::setup($rectorConfig, __DIR__, 12, PhpVersion::PHP_82);
};
