<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

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
});
