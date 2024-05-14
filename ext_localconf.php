<?php

use DigitalMarketingFramework\Typo3\Core\Backend\DataHandler\JsonDataHandler;
use DigitalMarketingFramework\Typo3\Core\Controller\FrontendController;
use DigitalMarketingFramework\Typo3\Core\Form\Element\JsonFieldElement;
use DigitalMarketingFramework\Typo3\Core\Routing\Enhancer\ResourceEnhancer;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3') || die();

call_user_func(static function () {

    // Json Field - pretty printed in backend UI, compressed in DB
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = JsonDataHandler::class;
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1673431342] = [
        'nodeName' => JsonFieldElement::RENDER_TYPE,
        'priority' => 40,
        'class' => JsonFieldElement::class,
    ];

    ExtensionUtility::configurePlugin(
        'DmfCore',
        'FrontendService',
        [
            FrontendController::class => 'javaScriptSettings',
        ],
        // non-cacheable actions
        [
        ]
    );

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['routing']['enhancers'][ResourceEnhancer::ENHANCER_NAME] = ResourceEnhancer::class;
});
