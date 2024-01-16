<?php

namespace DigitalMarketingFramework\Typo3\Core\Controller\Event;

class FrontendJavaScriptSettingsUpdateEvent
{
    /** @var array{settings:array<string,mixed>,urls:array<string,array<string,string>>,pluginSettings?:array<string,array<string,array<string,mixed>>>} */
    protected array $settings = [
        'settings' => [
            'prefix' => 'dmf',
        ],
        'urls' => [
        ],
    ];

    public function __construct(
        protected int $pageId,
        protected int $rootPageId,
    ) {
    }

    public function getPageId(): int
    {
        return $this->pageId;
    }

    public function getRootPageId(): int
    {
        return $this->rootPageId;
    }

    /**
     * @return array{settings:array<string,mixed>,urls:array<string,array<string,string>>,pluginSettings?:array<string,array<string,array<string,mixed>>>}
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    /**
     * @param array{settings:array<string,mixed>,urls:array<string,array<string,string>>,pluginSettings?:array<string,array<string,array<string,mixed>>>} $settings
     */
    public function setSettings(array $settings): void
    {
        $this->settings = $settings;
    }

    /**
     * @param array<string,mixed> $settings
     */
    public function addJavaScriptPlugin(string $type, string $plugin, string $url, array $settings = []): void
    {
        $this->settings['urls'][$type][$plugin] = $url;
        if ($settings !== []) {
            $this->settings['pluginSettings'][$type][$plugin] = $settings;
        }
    }

    public function addGlobalSettings(string $name, mixed $value): void
    {
        $this->settings['settings'][$name] = $value;
    }
}
