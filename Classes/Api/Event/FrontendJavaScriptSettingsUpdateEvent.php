<?php

namespace DigitalMarketingFramework\Typo3\Core\Api\Event;

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

    public function __construct()
    {
    }

    /**
     * @return array{settings:array<string,mixed>,urls:array<string,string>,pluginSettings?:array<string,array<string,mixed>>}
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    /**
     * @param array{settings:array<string,mixed>,urls:array<string,string>,pluginSettings?:array<string,array<string,mixed>>} $settings
     */
    public function setSettings(array $settings): void
    {
        $this->settings = $settings;
    }

    /**
     * @param array<string,mixed> $settings
     */
    public function addJavaScriptPlugin(string $id, string $url, array $settings = []): void
    {
        $this->settings['urls'][$id] = $url;
        if ($settings !== []) {
            $this->settings['pluginSettings'][$id] = $settings;
        }
    }

    public function addGlobalSettings(string $name, mixed $value): void
    {
        $this->settings['settings'][$name] = $value;
    }
}
