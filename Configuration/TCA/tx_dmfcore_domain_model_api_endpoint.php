<?php

use DigitalMarketingFramework\Typo3\Core\Form\Element\ConfigurationEditorTextFieldElement;

defined('TYPO3') || exit;

$ll = 'LLL:EXT:dmf_core/Resources/Private/Language/locallang_db.xlf:';

$GLOBALS['TCA']['tx_dmfcore_domain_model_api_endpoint'] = [
    'ctrl' => [
        'label' => 'name',
        'tstamp' => 'changed',
        'crdate' => 'created',
        'title' => $ll . 'tx_dmfcore_domain_model_api_endpoint',
        'origUid' => 't3_origuid',
        'searchFields' => 'path_segment',
        'iconfile' => 'EXT:dmf_core/Resources/Public/Icons/ApiEndPoint.svg',
        'default_sortby' => 'changed DESC',
    ],
    'types' => [
        '0' => [
            'showitem' => '--div--;General,name,enabled,expose_to_frontend,configuration_document,--div--;Push,push_enabled,disable_context,allow_context_override,--div--;Pull,pull_enabled',
        ],
    ],
    'palettes' => [
        '0' => ['showitem' => '--div--;General,name,enabled,expose_to_frontend,configuration_document,--div--;Push,push_enabled,disable_context,allow_context_override,--div--;Pull,pull_enabled'],
    ],
    'columns' => [
        'name' => [
            'exclude' => 1,
            'label' => $ll . 'tx_dmfcore_domain_model_api_endpoint.name',
            'config' => [
                'type' => 'input',
            ],
        ],
        'enabled' => [
            'exclude' => 1,
            'label' => $ll . 'tx_dmfcore_domain_model_api_endpoint.enabled',
            'config' => [
                'type' => 'check',
            ],
        ],
        'push_enabled' => [
            'exclude' => 1,
            'label' => $ll . 'tx_dmfcore_domain_model_api_endpoint.push_enabled',
            'config' => [
                'type' => 'check',
            ],
        ],
        'pull_enabled' => [
            'exclude' => 1,
            'label' => $ll . 'tx_dmfcore_domain_model_api_endpoint.pull_enabled',
            'config' => [
                'type' => 'check',
            ],
        ],
        'disable_context' => [
            'exclude' => 1,
            'label' => $ll . 'tx_dmfcore_domain_model_api_endpoint.disable_context',
            'config' => [
                'type' => 'check',
            ],
        ],
        'allow_context_override' => [
            'exclude' => 1,
            'label' => $ll . 'tx_dmfcore_domain_model_api_endpoint.allow_context_override',
            'config' => [
                'type' => 'check',
            ],
        ],
        'expose_to_frontend' => [
            'exclude' => 1,
            'label' => $ll . 'tx_dmfcore_domain_model_api_endpoint.expose_to_frontend',
            'config' => [
                'type' => 'check',
            ],
        ],
        'configuration_document' => [
            'exclude' => 1,
            'label' => $ll . 'tx_dmfcore_domain_model_api_endpoint.configuration_document',
            'config' => [
                'type' => 'user',
                'renderType' => ConfigurationEditorTextFieldElement::RENDER_TYPE,
                'cols' => 40,
                'rows' => 5,
            ],
        ],
    ],
];
