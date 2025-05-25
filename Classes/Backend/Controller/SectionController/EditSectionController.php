<?php

namespace DigitalMarketingFramework\Typo3\Core\Backend\Controller\SectionController;

use DigitalMarketingFramework\Core\Backend\Controller\SectionController\SectionController;
use DigitalMarketingFramework\Core\Backend\Response\RedirectResponse;
use DigitalMarketingFramework\Core\Backend\Response\Response;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

abstract class EditSectionController extends SectionController
{
    public const WEIGHT = 0;

    public function __construct(string $keyword, RegistryInterface $registry, string $section, array $routes = ['edit'])
    {
        parent::__construct($keyword, $registry, $section, $routes);
    }

    abstract protected function getTableName(): string;

    protected function editAction(): Response
    {
        $id = $this->getParameters()['id'] ?? '';
        $returnUrl = $this->getReturnUrl();

        $typo3UriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        $parameters = [
            'edit' => [$this->getTableName() => [$id => 'edit']],
            'returnUrl' => $returnUrl,
        ];
        $url = $typo3UriBuilder->buildUriFromRoute('record_edit', $parameters);

        return new RedirectResponse($url);
    }
}
