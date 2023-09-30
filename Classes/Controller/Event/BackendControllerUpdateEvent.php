<?php

namespace DigitalMarketingFramework\Typo3\Core\Controller\Event;

class BackendControllerUpdateEvent
{
    /** @var array<string,array<string>> */
    protected array $controllers = [];

    /**
     * @param array<string> $controllerActions
     */
    public function addControllerActions(string $controllerClassName, array $controllerActions): void
    {
        $this->controllers[$controllerClassName] ??= [];
        foreach ($controllerActions as $controllerAction) {
            if (!in_array($controllerAction, $this->controllers[$controllerClassName], true)) {
                $this->controllers[$controllerClassName][] = $controllerAction;
            }
        }
    }

    /**
     * @return array<string,string>|array<array<string>>
     */
    public function getControllersAndActions(bool $t3V11 = false): array
    {
        if ($t3V11) {
            return array_map(static function (array $actions): string {
                return implode(',', $actions);
            }, $this->controllers);
        }

        return $this->controllers;
    }
}
