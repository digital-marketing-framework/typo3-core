<?php

namespace DigitalMarketingFramework\Typo3\Core\Controller;

use DigitalMarketingFramework\Core\ConfigurationDocument\ConfigurationDocumentManagerInterface;
use DigitalMarketingFramework\Typo3\Core\Registry\Registry;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Form\Controller\AbstractBackendController;

class ConfigurationDocumentController extends AbstractBackendController
{
    protected ConfigurationDocumentManagerInterface $configurationDocumentManager;

    public function __construct(Registry $registry)
    {
        $this->configurationDocumentManager = $registry->getConfigurationDocumentManager();
    }

    public function createAction(string $documentName): ResponseInterface
    {
        $identifier = $this->configurationDocumentManager->getDocumentIdentifierFromBaseName($documentName);
        $this->configurationDocumentManager->createDocument($identifier, '', $documentName);
        $this->redirect(actionName:'edit', arguments:['documentIdentifier' => $identifier]);
        return $this->htmlResponse();
    }

    public function listAction(): ResponseInterface
    {
        $list = [];
        $documentIdentifiers = $this->configurationDocumentManager->getDocumentIdentifiers();
        foreach ($documentIdentifiers as $documentIdentifier) {
            $list[$documentIdentifier] = $this->configurationDocumentManager->getMetaDataFromIdentifier($documentIdentifier);
        }
        $this->view->assign('documents', $list);
        return $this->htmlResponse();
    }

    public function editAction(string $documentIdentifier): ResponseInterface
    {
        $document = $this->configurationDocumentManager->getMetaDataFromIdentifier($documentIdentifier);
        $document['content'] = $this->configurationDocumentManager->getDocumentFromIdentifier($documentIdentifier);
        $this->view->assign('document', $document);

        return $this->htmlResponse();
    }

    public function saveAction(string $documentIdentifier, string $document): ResponseInterface
    {
        $this->configurationDocumentManager->saveDocument($documentIdentifier, $document);
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
