<?php

namespace DigitalMarketingFramework\Typo3\Core\Domain\Model\Api;

use DigitalMarketingFramework\Core\Model\Api\EndPointInterface;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class EndPoint extends AbstractEntity implements EndPointInterface
{
    public function __construct(
        protected string $name = '',
        protected bool $enabled = false,
        protected bool $pushEnabled = false,
        protected bool $pullEnabled = false,
        protected bool $disableContext = false,
        protected bool $allowContextOverride = false,
        protected bool $exposeToFrontend = false,
        protected string $configurationDocument = '',
    ) {
    }

    public function getLabel(): string
    {
        return $this->getName();
    }

    public function getId(): ?int
    {
        return $this->uid;
    }

    public function setId(int $id): void
    {
        $this->uid = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    public function getPushEnabled(): bool
    {
        return $this->pushEnabled;
    }

    public function setPushEnabled(bool $pushEnabled): void
    {
        $this->pushEnabled = $pushEnabled;
    }

    public function getPullEnabled(): bool
    {
        return $this->pullEnabled;
    }

    public function setPullEnabled(bool $pullEnabled): void
    {
        $this->pullEnabled = $pullEnabled;
    }

    public function getDisableContext(): bool
    {
        return $this->disableContext;
    }

    public function setDisableContext(bool $disableContext): void
    {
        $this->disableContext = $disableContext;
    }

    public function getAllowContextOverride(): bool
    {
        return $this->allowContextOverride;
    }

    public function setAllowContextOverride(bool $allowContextOverride): void
    {
        $this->allowContextOverride = $allowContextOverride;
    }

    public function getExposeToFrontend(): bool
    {
        return $this->exposeToFrontend;
    }

    public function setExposeToFrontend(bool $exposeToFrontend): void
    {
        $this->exposeToFrontend = $exposeToFrontend;
    }

    public function getConfigurationDocument(): string
    {
        return $this->configurationDocument;
    }

    public function setConfigurationDocument(string $configurationDocument): void
    {
        $this->configurationDocument = $configurationDocument;
    }
}
