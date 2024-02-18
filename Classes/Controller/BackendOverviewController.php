<?php

namespace DigitalMarketingFramework\Typo3\Core\Controller;

use Psr\Http\Message\ResponseInterface;

class BackendOverviewController extends AbstractBackendController
{
    public const SECTION_DEFAULT_WEIGHT = 50;

    public function showAction(): ResponseInterface
    {
        $sections = $this->settings['sections'] ?? [];
        usort($sections, function(array $section1, array $section2) {
            return ($section1['weight'] ?? static::SECTION_DEFAULT_WEIGHT) <=> ($section2['weight'] ?? static::SECTION_DEFAULT_WEIGHT);
        });
        $this->view->assign('sections', $sections);

        return $this->backendHtmlResponse();
    }
}
