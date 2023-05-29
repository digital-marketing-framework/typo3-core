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

    protected function getResetConfig(): array
    {
        $reset = [];
        $defaults = $this->getDefaults();
        foreach (array_keys($defaults) as $key) {
            $reset[$key] = null;
        }
        return $reset;
    }

    protected function getResetDocument(bool $metaDataOnly = false): string
    {
        $metaData = ['name' => 'Reset'];
        $config = $metaDataOnly ? [] : $this->getResetConfig();
        return $this->parser->produceDocument($metaData + $config);
    }

    protected function getDefaultsDocument(bool $metaDataOnly = false): string
    {
        $metaData = ['name' => 'Defaults'];
        $config = $metaDataOnly ? [] : $this->getDefaults();
        return $this->parser->produceDocument($metaData + $config);
    }

    protected function getDocument(string $documentIdentifier, bool $metaDataOnly = false): ?string
    {
        switch ($documentIdentifier) {
            case static::ID_DEFAULTS:
                return $this->getDefaultsDocument($metaDataOnly);
            case static::ID_RESET:
                return $this->getResetDocument($metaDataOnly);
            default:
                return null;
        }
    }
}
