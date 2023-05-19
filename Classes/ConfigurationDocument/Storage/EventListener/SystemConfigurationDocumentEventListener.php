<?php

namespace DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Storage\EventListener;

use DigitalMarketingFramework\Core\ConfigurationDocument\Parser\ConfigurationDocumentParserInterface;
use DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Event\ConfigurationDocumentMetaDataUpdateEvent;
use DigitalMarketingFramework\Typo3\Core\Registry\Registry;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;

abstract class SystemConfigurationDocumentEventListener
{
    public const ID_DEFAULTS = 'SYS:defaults';
    public const ID_RESET = 'SYS:reset';

    protected ConfigurationDocumentParserInterface $parser;

    public function __construct(
        protected EventDispatcher $eventDispatcher,
        Registry $registry,
    ) {
        $this->parser = $registry->getConfigurationDocumentParser();
    }

    protected function getIdentifiers(): array
    {
        return [
            static::ID_DEFAULTS,
            static::ID_RESET,
        ];
    }

    protected function getDefaults(): array
    {
        $event = new ConfigurationDocumentMetaDataUpdateEvent();
        $this->eventDispatcher->dispatch($event);
        return $event->getDefaultConfiguration();

    }

    protected function getResetDocument(): string
    {
        $defaults = $this->getDefaults();
        foreach (array_keys($defaults) as $key) {
            $defaults[$key] = null;
        }
        return $this->parser->produceDocument(['name' => 'Reset'] + $defaults);
    }

    protected function getDefaultsDocument(): string
    {
        return $this->parser->produceDocument(['name' => 'Defaults'] + $this->getDefaults());
    }

    protected function getDocument(string $documentIdentifier): ?string
    {
        switch ($documentIdentifier) {
            case static::ID_DEFAULTS:
                return $this->getDefaultsDocument();
            case static::ID_RESET:
                return $this->getResetDocument();
            default:
                return null;
        }
    }
}
