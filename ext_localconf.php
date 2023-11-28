<?php

use DigitalMarketingFramework\Typo3\Core\Controller\FrontendController;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
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
