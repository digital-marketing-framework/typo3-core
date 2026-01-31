<?php

namespace DigitalMarketingFramework\Typo3\Core\Crypto;

use DigitalMarketingFramework\Core\Crypto\HashServiceInterface;
use TYPO3\CMS\Core\Crypto\HashService as Typo3V13HashService;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Security\Cryptography\HashService as Typo3V12HashService;

class HashService implements HashServiceInterface
{
    public function generateHash(string $subject, string $additionalSecret): string
    {
        $version = new Typo3Version();
        if ($version->getMajorVersion() <= 12) {
            $hashService = GeneralUtility::makeInstance(Typo3V12HashService::class);

            return $hashService->generateHmac($subject . $additionalSecret);
        }

        // @phpstan-ignore-next-line TYPO3 version switch
        $hashService = GeneralUtility::makeInstance(Typo3V13HashService::class);

        // @phpstan-ignore-next-line TYPO3 version switch
        return $hashService->hmac($subject, $additionalSecret);
    }

    public function validateHash(string $subject, string $additionalSecret, string $hash): bool
    {
        $version = new Typo3Version();
        if ($version->getMajorVersion() <= 12) {
            $hashService = GeneralUtility::makeInstance(Typo3V12HashService::class);

            return $hashService->validateHmac($subject . $additionalSecret, $hash);
        }

        // @phpstan-ignore-next-line TYPO3 version switch
        $hashService = GeneralUtility::makeInstance(Typo3V13HashService::class);

        // @phpstan-ignore-next-line TYPO3 version switch
        return $hashService->validateHmac($subject, $additionalSecret, $hash);
    }
}
