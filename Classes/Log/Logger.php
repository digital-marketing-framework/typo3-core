<?php

namespace DigitalMarketingFramework\Typo3\Core\Log;

use DigitalMarketingFramework\Core\Log\LoggerInterface;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

class Logger implements LoggerInterface
{
    public function __construct(
        protected PsrLoggerInterface $logger,
    ) {
    }

    public function debug(string $msg): void
    {
        $this->logger->debug($msg);
    }

    public function info(string $msg): void
    {
        $this->logger->info($msg);
    }

    public function warning(string $msg): void
    {
        $this->logger->warning($msg);
    }

    public function error(string $msg): void
    {
        $this->logger->error($msg);
    }
}
