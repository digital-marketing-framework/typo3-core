<?php

namespace DigitalMarketingFramework\Typo3\Core\Controller;

use DigitalMarketingFramework\Core\ConfigurationDocument\Controller\FullDocumentConfigurationEditorController;
use DigitalMarketingFramework\Typo3\Core\ConfigurationDocument\Event\ConfigurationDocumentMetaDataUpdateEvent;
use DigitalMarketingFramework\Typo3\Core\Registry\Registry;
use Psr\Http\Message\ResponseFactoryInterface;

class GlobalConfigurationAjaxController extends AbstractAjaxController
{
    public function __construct(
        ResponseFactoryInterface $responseFactory,
        Registry $registry,
    ) {
        $parser = $registry->getConfigurationDocumentParser();
        $schemaDocument = $registry->getSchemaDocument(Registry::SCHEMA_KEY_GLOBAL_CONFIGURATION);
        $editorController = new FullDocumentConfigurationEditorController($schemaDocument);

        parent::__construct($responseFactory, $parser, $editorController);
    }
}
