<?php

use DigitalMarketingFramework\Typo3\Core\Controller\FrontendController;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3') || die();

call_user_func(static function () {
    $extensionKey = 'dmf_core';
    ExtensionManagementUtility::addTypoScript(
        $extensionKey,
        'constants',
        "@import 'EXT:dmf_core/Configuration/TypoScript/constants.typoscript'"
    );
    ExtensionManagementUtility::addTypoScript(
        $extensionKey,
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
            // FrontEndController::class => 'javaScriptSettings',
        ]
    );
});
