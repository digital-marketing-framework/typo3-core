<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') || die();

call_user_func(static function () {
    $extensionKey = 'digitalmarketingframework';
    ExtensionManagementUtility::addTypoScript(
        $extensionKey,
        'constants',
        "@import 'EXT:digitalmarketingframework/Configuration/TypoScript/constants.typoscript'"
    );
    ExtensionManagementUtility::addTypoScript(
        $extensionKey,
        'setup',
        "@import 'EXT:digitalmarketingframework/Configuration/TypoScript/setup.typoscript'"
    );
});
