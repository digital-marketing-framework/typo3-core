<?php

namespace DigitalMarketingFramework\Typo3\Core\Controller;

use DigitalMarketingFramework\Core\ConfigurationDocument\Controller\InheritedDocumentConfigurationEditorController;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Typo3\Core\Registry\RegistryCollection;
use Psr\Http\Message\ResponseFactoryInterface;

class ConfigurationDocumentAjaxController extends AbstractAjaxController
{
    public function __construct(
        ResponseFactoryInterface $responseFactory,
        RegistryCollection $registryCollection,
    ) {
        $registry = $registryCollection->getRegistryByClass(RegistryInterface::class);
        $schemaDocument = $registryCollection->getConfigurationSchemaDocument();
        $editorController = $registry->createObject(InheritedDocumentConfigurationEditorController::class);
        $editorController->setSchemaDocument($schemaDocument);

        parent::__construct($responseFactory, $editorController);
    }
}
