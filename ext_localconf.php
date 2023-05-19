<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') or die();

call_user_func(function()
{
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
