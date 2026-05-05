<?php

namespace DigitalMarketingFramework\Typo3\Core\Frontend;

use DigitalMarketingFramework\Core\Frontend\FrontendUriBuilderInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Typolink\LinkFactory;

class Typo3FrontendUriBuilder implements FrontendUriBuilderInterface
{
    protected ?LinkFactory $linkFactory = null;

    protected function getLinkFactory(): LinkFactory
    {
        if (!$this->linkFactory instanceof LinkFactory) {
            $this->linkFactory = GeneralUtility::makeInstance(LinkFactory::class);
        }

        return $this->linkFactory;
    }

    public function build(string $uri): string
    {
        // A bare numeric value is shorthand for a page UID, matching the
        // convention of TYPO3's core RedirectFinisher.
        if (ctype_digit($uri)) {
            $uri = 't3://page?uid=' . $uri;
        }

        return $this->getLinkFactory()->createUri($uri)->getUrl();
    }
}
