<?php

use DigitalMarketingFramework\Typo3\Core\Backend\DataHandler\JsonDataHandler;
use DigitalMarketingFramework\Typo3\Core\Controller\FrontendController;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use DigitalMarketingFramework\Typo3\Core\Form\Element\JsonFieldElement;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3') || die();

call_user_func(static function () {
    ExtensionManagementUtility::addTypoScript(
        'dmf_core',
        'constants',
        "@import 'EXT:dmf_core/Configuration/TypoScript/constants.typoscript'"
    );
    ExtensionManagementUtility::addTypoScript(
        'dmf_core',
        'setup',
        "@import 'EXT:dmf_core/Configuration/TypoScript/setup.typoscript'"
    );

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
});
