<?php

namespace DigitalMarketingFramework\Typo3\Core\Controller;

use DigitalMarketingFramework\Core\ConfigurationDocument\Controller\InheritedDocumentConfigurationEditorController;
use DigitalMarketingFramework\Core\Registry\RegistryCollection;
use DigitalMarketingFramework\Typo3\Core\Registry\Registry;
use Psr\Http\Message\ResponseFactoryInterface;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;

class ConfigurationDocumentAjaxController extends AbstractAjaxController
{
    public function __construct(
        ResponseFactoryInterface $responseFactory,
        EventDispatcher $eventDispatcher,
        Registry $registry,
    ) {
        $registryCollection = new RegistryCollection();
        $eventDispatcher->dispatch($registryCollection);

        $schemaDocument = $registryCollection->getConfigurationSchemaDocument();
        $editorController = $registry->createObject(InheritedDocumentConfigurationEditorController::class);
        $editorController->setSchemaDocument($schemaDocument);

        parent::__construct($responseFactory, $editorController);
    }
}
