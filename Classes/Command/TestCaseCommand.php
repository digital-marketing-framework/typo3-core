<?php

namespace DigitalMarketingFramework\Typo3\Core\Command;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\TestCase\TestResult;
use DigitalMarketingFramework\Typo3\Core\Registry\RegistryCollection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCaseCommand extends Command
{
    public function __construct(
        protected RegistryCollection $registryCollection,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setHelp('Run all Anyrel test cases.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $testCaseManager = $this->registryCollection->getRegistry()->getTestCaseManager();
        $results = $testCaseManager->runAllTests();

        $total = 0;
        $failed = 0;
        $errored = 0;
        $outdated = 0;
        foreach ($results as $result) {
            $total++;

            if ($result->getTest()->getLabel() !== '' && $result->getTest()->getLabel() !== $result->getTest()->getName()) {
                $label = sprintf('%s (%s)', $result->getTest()->getLabel(), $result->getTest()->getName());
            } else {
                $label = $result->getTest()->getName();
            }

            switch ($result->getStatus()) {
                case TestResult::STATUS_SUCCESS:
                    $output->writeln(sprintf('Test case %s succeeded.', $label));
                    break;
                case TestResult::STATUS_OUTDATED:
                    $output->writeln(sprintf('Test case %s seems to be outdated.', $label));
                    $outdated++;
                    break;
                case TestResult::STATUS_FAIL:
                    $output->writeln(sprintf('Test case %s" failed.', $label));
                    $output->writeln(sprintf('Output: %s', json_encode($result->getOutput())));
                    $output->writeln(sprintf('Expected output: %s', json_encode($result->getTest()->getExpectedOutput())));
                    $failed++;
                    break;
                case TestResult::STATUS_ERROR:
                    $output->writeln(sprintf('Test case %s errored.', $label));
                    $output->writeln(sprintf('Message: %s', $result->getError()));
                    $errored++;
                    break;
                default:
                    throw new DigitalMarketingFrameworkException(sprintf('Unknown test result status: %d', $result->getStatus()));
            }
        }
        $output->writeln('');

        $succeeded = $total - $failed - $errored - $outdated;

        $summary = sprintf('Total: %d, Success: %d, Outdated: %d, Fail: %d, Error: %d', $total, $succeeded, $outdated, $failed, $errored);
        $output->writeln($summary);

        return $succeeded === $total
            ? Command::SUCCESS
            : Command::FAILURE;
    }
}
