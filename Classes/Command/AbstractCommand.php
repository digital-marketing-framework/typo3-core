<?php

namespace DigitalMarketingFramework\Typo3\Core\Command;

use DigitalMarketingFramework\Typo3\Core\Utility\CliEnvironmentUtility;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractCommand extends Command
{
    final protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return CliEnvironmentUtility::ensureBackendRequest(
            fn () => $this->executeCommand($input, $output)
        );
    }

    abstract protected function executeCommand(InputInterface $input, OutputInterface $output): int;
}
