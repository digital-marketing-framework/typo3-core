<?php

namespace DigitalMarketingFramework\Typo3\Core\Controller\Event;

class FrontendJavaScriptSettingsUpdateEvent
{
    /** @var array{settings:array{prefix:string},urls:array<string,array<string,string>>,pluginSettings:array<string,array<string,array<string,mixed>>>} */
    protected array $settings = [
        'settings' => [
            'prefix' => 'dmf',
        ],
        'urls' => [
        ],
        'pluginSettings' => [
        ],
    ];

    public function __construct(
        protected int $pageId,
        protected int $rootPageId,
    ) {
    }

    public function getPageid(): int
    {
        return $this->pageId;
    }

    public function getRootPageid(): int
    {
        return $this->rootPageId;
    }

    public function getSettings(): array
    {
        return $this->settings;
    }

    public function setSettings(array $settings): void
    {
        $this->settings = $settings;
    }

    public function addJavaScriptPlugin(string $type, string $plugin, string $url, array $settings = []): void
    {
        $this->settings['urls'][$type][$plugin] = $url;
        $this->settings['pluginSettings'][$type][$plugin] = $settings;
    }
}
