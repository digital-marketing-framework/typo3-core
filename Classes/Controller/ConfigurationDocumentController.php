<?php

namespace DigitalMarketingFramework\Typo3\Core\Controller;

use DigitalMarketingFramework\Core\ConfigurationDocument\ConfigurationDocumentManagerInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\SchemaDocument;
use DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Event\ConfigurationDocumentMetaDataUpdateEvent;
use DigitalMarketingFramework\Typo3\Core\Registry\Registry;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Form\Controller\AbstractBackendController;

class ConfigurationDocumentController extends AbstractBackendController
{
    protected ConfigurationDocumentManagerInterface $configurationDocumentManager;
    protected SchemaDocument $schemaDocument;

    public function __construct(
        Registry $registry,
        EventDispatcher $eventDispatcher,
    ) {
        $this->configurationDocumentManager = $registry->getConfigurationDocumentManager();
        $event = new ConfigurationDocumentMetaDataUpdateEvent();
        $eventDispatcher->dispatch($event);
        $this->schemaDocument = $event->getSchemaDocument();
    }

    public function createAction(string $documentName): ResponseInterface
    {
        $identifier = $this->configurationDocumentManager->getDocumentIdentifierFromBaseName($documentName);
        $this->configurationDocumentManager->createDocument($identifier, '', $documentName, $this->schemaDocument);
        $this->redirect(actionName:'edit', arguments:['documentIdentifier' => $identifier]);
        return $this->htmlResponse();
    }

    public function listAction(): ResponseInterface
    {
        $list = [];
        $documentIdentifiers = $this->configurationDocumentManager->getDocumentIdentifiers();
        foreach ($documentIdentifiers as $documentIdentifier) {
            $list[$documentIdentifier] = $this->configurationDocumentManager->getDocumentInformation($documentIdentifier);
        }
        $this->view->assign('documents', $list);
        return $this->htmlResponse();
    }

    public function editAction(string $documentIdentifier): ResponseInterface
    {
        $document = $this->configurationDocumentManager->getDocumentInformation($documentIdentifier);
        $document['content'] = $this->configurationDocumentManager->getDocumentFromIdentifier($documentIdentifier);
        $this->view->assign('document', $document);
        return $this->htmlResponse();
    }

    public function saveAction(string $documentIdentifier, string $document): ResponseInterface
    {
        $this->configurationDocumentManager->saveDocument($documentIdentifier, $document, $this->schemaDocument);
        $this->redirect(actionName:'edit', arguments:['documentIdentifier' => $documentIdentifier]);
        return $this->htmlResponse();
    }

    public function deleteAction(string $documentIdentifier): ResponseInterface
    {
        $this->configurationDocumentManager->deleteDocument($documentIdentifier);
        $this->redirect(actionName:'list');
        return $this->htmlResponse();
    }
}
