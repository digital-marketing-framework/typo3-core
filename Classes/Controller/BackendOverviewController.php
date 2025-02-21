<?php

namespace DigitalMarketingFramework\Typo3\Core\Controller;

use DigitalMarketingFramework\Core\Model\Alert\AlertInterface;
use DigitalMarketingFramework\Typo3\Core\Registry\RegistryCollection;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Imaging\IconFactory;

class BackendOverviewController extends AbstractBackendController
{
    public function __construct(
        ModuleTemplateFactory $moduleTemplateFactory,
        IconFactory $iconFactory,
        protected RegistryCollection $registryCollection
    ) {
        parent::__construct($moduleTemplateFactory, $iconFactory);
    }

    /**
     * @return array<array{title:?string,content:string,source:string,state:int}>
     */
    protected function getAlerts(): array
    {
        $messages = [];
        foreach ($this->registryCollection->getAlertManager()->getAllAlerts() as $message) {
            $messages[] = [
                'title' => $message->getTitle(),
                'content' => $message->getContent(),
                'source' => $message->getSource(),
                'state' => match ($message->getType()) {
                    AlertInterface::TYPE_INFO => 0,
                    AlertInterface::TYPE_WARNING => 1,
                    AlertInterface::TYPE_ERROR => 2,
                    default => 0,
                }
            ];
        }

        return $messages;
    }

    public function showAction(): ResponseInterface
    {
        $this->view->assign('alerts', $this->getAlerts());
        $this->view->assign('sections', $this->getSections());

        return $this->backendHtmlResponse();
    }
}
