<?php

namespace DigitalMarketingFramework\Typo3\Core\Controller;

use DigitalMarketingFramework\Core\ConfigurationDocument\Controller\FullDocumentConfigurationEditorController;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Typo3\Core\Registry\RegistryCollection;
use Psr\Http\Message\ResponseFactoryInterface;

class GlobalConfigurationAjaxController extends AbstractAjaxController
{
    public function __construct(
        ResponseFactoryInterface $responseFactory,
        RegistryCollection $registryCollection,
    ) {
        $registry = $registryCollection->getRegistryByClass(RegistryInterface::class);
        $schemaDocument = $registryCollection->getGlobalConfigurationSchemaDocument();
        $editorController = $registry->createObject(FullDocumentConfigurationEditorController::class);
        $editorController->setSchemaDocument($schemaDocument);

        parent::__construct($responseFactory, $editorController);
    }
}
