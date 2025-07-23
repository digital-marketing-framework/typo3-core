<?php

namespace DigitalMarketingFramework\Typo3\Core\Crypto;

use DigitalMarketingFramework\Core\Crypto\HashServiceInterface;
use TYPO3\CMS\Core\Crypto\HashService as Typo3HashService;

class HashService implements HashServiceInterface
{
    public function __construct(
        protected Typo3HashService $hashService,
    ) {
    }

    public function generateHash(string $subject, string $additionalSecret): string
    {
        return $this->hashService->hmac($subject, $additionalSecret);
    }

    public function validateHash(string $subject, string $additionalSecret, string $hash): bool
    {
        return $this->hashService->validateHmac($subject, $additionalSecret, $hash);
    }
}
