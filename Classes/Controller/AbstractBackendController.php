<?php

namespace DigitalMarketingFramework\Typo3\Core\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\Components\Menu\Menu;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Http\RedirectResponse;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Form\Controller\AbstractBackendController as OriginalAbstractBackendController;

class AbstractBackendController extends OriginalAbstractBackendController
{
    public function __construct(
        protected ModuleTemplateFactory $moduleTemplateFactory,
        protected IconFactory $iconFactory,
    ) {
    }

    protected function backendHtmlResponse(string $title = 'Digital Marketing'): ResponseInterface
    {
        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        $this->initializeModuleTemplate($moduleTemplate);

        $moduleTemplate->setModuleClass($this->request->getPluginName() . '_' . $this->request->getControllerName());
        $moduleTemplate->setFlashMessageQueue($this->getFlashMessageQueue());
        $moduleTemplate->setTitle($this->getLanguageService()->sL($title));
        $moduleTemplate->setContent($this->view->render());

        return $this->htmlResponse($moduleTemplate->renderContent());
    }

    /**
     * @param ?array<string,mixed> $arguments
     */
    protected function redirectResponse(string $action, ?string $controller = null, ?array $arguments = null): RedirectResponse
    {
        $uri = $this->uriBuilder->reset()->uriFor(
            actionName: $action,
            controllerName: $controller,
            controllerArguments: $arguments
        );

        return new RedirectResponse($uri);
    }

    /**
     * @param array{title:string,controller:string,action:string} $section
     */
    protected function sectionIsActive(array $section): bool
    {
        return $this->request->getControllerName() === $section['controller'];
    }

    protected function buildSectionMenu(Menu $sectionMenu): void
    {
        $sectionMenu->setIdentifier('dmfSectionMenu');
        $sectionMenu->setLabel('');

        $sections = $this->settings['sections'] ?? [];
        array_unshift($sections, [
            'title' => 'Overview',
            'controller' => 'BackendOverview',
            'action' => 'show',
        ]);

        foreach ($sections as $section) {
            $menuItem = $sectionMenu->makeMenuItem();
            $url = $this->uriBuilder->uriFor(
                actionName: $section['action'],
                controllerName: $section['controller']
            );
            $menuItem->setHref($url);
            $menuItem->setTitle($section['title']);
            if ($this->sectionIsActive($section)) {
                $menuItem->setActive(true);
            }

            $sectionMenu->addMenuItem($menuItem);
        }
    }

    protected function addShortcutButton(ButtonBar $buttonBar): void
    {
        $shortcutButton = $buttonBar->makeShortcutButton()
            ->setRouteIdentifier('web_DmfCoreManager')
            ->setDisplayName($this->getLanguageService()->sL('LLL:EXT:dmf_core/Resources/Private/Language/Database.xlf:module.shortcut_name'));
        $buttonBar->addButton($shortcutButton, ButtonBar::BUTTON_POSITION_RIGHT);
    }

    protected function addReloadButton(ButtonBar $buttonBar): void
    {
        $reloadButton = $buttonBar->makeLinkButton()
            ->setHref($this->request->getAttribute('normalizedParams')->getRequestUri())
            ->setTitle($this->getLanguageService()->sL('LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:labels.reload'))
            ->setIcon($this->iconFactory->getIcon('actions-refresh', Icon::SIZE_SMALL));
        $buttonBar->addButton($reloadButton, ButtonBar::BUTTON_POSITION_RIGHT);
    }

    protected function addActionButtons(ButtonBar $buttonBar): void
    {
        $this->addShortcutButton($buttonBar);
    }

    protected function initializeModuleTemplate(ModuleTemplate $moduleTemplate): void
    {
        $sectionMenu = $moduleTemplate->getDocHeaderComponent()->getMenuRegistry()->makeMenu();
        $this->buildSectionMenu($sectionMenu);
        $moduleTemplate->getDocHeaderComponent()->getMenuRegistry()->addMenu($sectionMenu);

        $buttonBar = $moduleTemplate->getDocHeaderComponent()->getButtonBar();
        $this->addActionButtons($buttonBar);
    }

    /**
     * Returns the Language Service
     *
     * @return LanguageService
     */
    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
