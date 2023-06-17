<?php

namespace DigitalMarketingFramework\Typo3\Core\Controller\Event;

class BackendControllerUpdateEvent
{
    protected array $controllers = [];

    public function addControllerActions(string $controllerClassName, array $controllerActions): void
    {
        $this->controllers[$controllerClassName] = $this->controllers[$controllerClassName] ?? [];
        foreach ($controllerActions as $controllerAction) {
            if (!in_array($controllerAction, $this->controllers[$controllerClassName])) {
                $this->controllers[$controllerClassName][] = $controllerAction;
            }
        }
    }

    public function getControllersAndActions(bool $t3V11 = false): array
    {
        if ($t3V11) {
            return array_map(function(array $actions) {
                return implode(',', $actions);
            }, $this->controllers);
        }
        return $this->controllers;
    }
}
