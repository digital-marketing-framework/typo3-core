<?php

namespace DigitalMarketingFramework\Typo3\Core\Domain\Model;

class ExampleItem extends Item
{
    public function __construct(
        protected string $name = '',
        protected string $description = '',
    ) {
    }

    public function getLabel(): string
    {
        return $this->getName();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
}
