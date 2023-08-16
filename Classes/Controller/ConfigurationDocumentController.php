<?php

namespace DigitalMarketingFramework\Typo3\Core\Controller;

use DigitalMarketingFramework\Core\ConfigurationDocument\ConfigurationDocumentManagerInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\SchemaDocument;
use DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Event\ConfigurationDocumentMetaDataUpdateEvent;
use DigitalMarketingFramework\Typo3\Core\Registry\Registry;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Core\Http\RedirectResponse;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Extbase\Http\ForwardResponse;

class ConfigurationDocumentController extends AbstractBackendController
{
    protected ConfigurationDocumentManagerInterface $configurationDocumentManager;
    protected SchemaDocument $schemaDocument;

    public function __construct(
        ModuleTemplateFactory $moduleTemplateFactory,
        IconFactory $iconFactory,
        Registry $registry,
        EventDispatcher $eventDispatcher,
    ) {
        parent::__construct($moduleTemplateFactory, $iconFactory);
        $this->configurationDocumentManager = $registry->getConfigurationDocumentManager();
        $event = new ConfigurationDocumentMetaDataUpdateEvent();
        $eventDispatcher->dispatch($event);
        $this->schemaDocument = $event->getSchemaDocument();
    }

    protected function redirectResponse(string $action, ?array $arguments = null): RedirectResponse
    {
        $uri = $this->uriBuilder->reset()->uriFor(actionName:$action, controllerArguments:$arguments);
        return new RedirectResponse($uri);
    }

    protected function addActionButtons(ButtonBar $buttonBar): void
    {
        parent::addActionButtons($buttonBar);
        $this->addReloadButton($buttonBar);
    }

    public function createAction(string $documentName): ResponseInterface
    {
        $documentIdentifier = $this->configurationDocumentManager->getDocumentIdentifierFromBaseName($documentName);
        $this->configurationDocumentManager->createDocument($documentIdentifier, '', $documentName, $this->schemaDocument);
        return $this->redirectResponse('edit', ['documentIdentifier' => $documentIdentifier]);
    }

    public function listAction(): ResponseInterface
    {
        $list = [];
        $documentIdentifiers = $this->configurationDocumentManager->getDocumentIdentifiers();
        foreach ($documentIdentifiers as $documentIdentifier) {
            $list[$documentIdentifier] = $this->configurationDocumentManager->getDocumentInformation($documentIdentifier);
        }
        $this->view->assign('documents', $list);
        return $this->backendHtmlResponse();
    }

    public function editAction(string $documentIdentifier): ResponseInterface
    {
        $document = $this->configurationDocumentManager->getDocumentInformation($documentIdentifier);
        $document['content'] = $this->configurationDocumentManager->getDocumentFromIdentifier($documentIdentifier);
        $this->view->assign('document', $document);
        return $this->backendHtmlResponse();
    }

    public function saveAction(string $documentIdentifier, string $document): ResponseInterface
    {
        $this->configurationDocumentManager->saveDocument($documentIdentifier, $document, $this->schemaDocument);
        return $this->redirectResponse('edit', ['documentIdentifier' => $documentIdentifier]);
    }

    public function deleteAction(string $documentIdentifier): ResponseInterface
    {
        $this->configurationDocumentManager->deleteDocument($documentIdentifier);
        return $this->redirectResponse('list');
    }
}
