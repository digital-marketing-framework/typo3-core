<?php

namespace DigitalMarketingFramework\Typo3\Core\Command;

use DigitalMarketingFramework\Typo3\Core\Registry\RegistryCollection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class CleanupCommand extends Command
{
    protected function configure(): void
    {
        $this->setHelp('Execute all Anyrel cleanup tasks.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $registryCollection = GeneralUtility::makeInstance(RegistryCollection::class);
        $cleanupManager = $registryCollection->getRegistry()->getCleanupManager();

        $cleanupManager->cleanup();

        $output->writeln('Anyrel cleanup tasks executed.');

        return Command::SUCCESS;
    }
}
