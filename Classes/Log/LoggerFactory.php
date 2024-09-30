<?php

namespace DigitalMarketingFramework\Typo3\Core\Log;

use DigitalMarketingFramework\Core\Log\LoggerFactoryInterface;
use DigitalMarketingFramework\Core\Log\LoggerInterface;
use TYPO3\CMS\Core\Log\LogManagerInterface;

class LoggerFactory implements LoggerFactoryInterface
{
    public function __construct(
        protected LogManagerInterface $logManager,
    ) {
    }

    public function getLogger(string $forClass): LoggerInterface
    {
        return new Logger($this->logManager->getLogger($forClass));
    }
}
