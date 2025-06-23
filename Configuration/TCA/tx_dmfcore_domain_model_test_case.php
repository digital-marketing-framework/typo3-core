<?php

use DigitalMarketingFramework\Typo3\Core\Form\Element\JsonFieldElement;

defined('TYPO3') || exit;

$ll = 'LLL:EXT:dmf_core/Resources/Private/Language/locallang_db.xlf:';

$GLOBALS['TCA']['tx_dmfcore_domain_model_test_case'] = [
    'ctrl' => [
        'label' => 'label',
        'tstamp' => 'changed',
        'crdate' => 'created',
        'title' => $ll . 'tx_dmfcore_domain_model_test_case',
        'origUid' => 't3_origuid',
        'searchFields' => 'name,description,type,created,changed',
        'iconfile' => 'EXT:dmf_core/Resources/Public/Icons/TestCase.svg',
        'default_sortby' => 'changed DESC',
    ],
    'types' => [
        '0' => [
            'showitem' => 'label,name,description,type,hash,created,changed,serialized_input,serialized_expected_output',
        ],
    ],
    'palettes' => [
        '0' => ['showitem' => 'label,name,description,type,hash,created,changed,serialized_input,serialized_expected_output'],
    ],
    'columns' => [
        'label' => [
            'exclude' => 1,
            'label' => $ll . 'tx_dmfcore_domain_model_test_case.label',
            'config' => [
                'type' => 'input',
            ],
        ],
        'name' => [
            'exclude' => 1,
            'label' => $ll . 'tx_dmfcore_domain_model_test_case.name',
            'config' => [
                'type' => 'input',
            ],
        ],
        'description' => [
            'exclude' => 1,
            'label' => $ll . 'tx_dmfcore_domain_model_test_case.description',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
            ],
        ],
        'type' => [
            'exclude' => 1,
            'label' => $ll . 'tx_dmfcore_domain_model_test_case.type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'itemsProcFunc' => 'DigitalMarketingFramework\Typo3\Core\Tca\TestCaseItemsProcFunc->getAllTestCaseTypes',
                'minitems' => 0,
                'maxitems' => 1,
                'size' => 1,
            ],
        ],
        'hash' => [
            'exclude' => 1,
            'label' => $ll . 'tx_dmfcore_domain_model_test_case.hash',
            'config' => [
                'type' => 'input',
            ],
        ],
        'created' => [
            'exclude' => 1,
            'label' => $ll . 'tx_dmfcore_domain_model_test_case.created',
            'config' => [
                'type' => 'datetime',
            ],
        ],
        'changed' => [
            'exclude' => 1,
            'label' => $ll . 'tx_dmfcore_domain_model_test_case.changed',
            'config' => [
                'type' => 'datetime',
            ],
        ],
        'serialized_input' => [
            'exclude' => 1,
            'label' => $ll . 'tx_dmfcore_domain_model_test_case.serialized_input',
            'config' => [
                'type' => 'user',
                'renderType' => JsonFieldElement::RENDER_TYPE,
                'cols' => 40,
                'rows' => 15,
            ],
        ],
        'serialized_expected_output' => [
            'exclude' => 1,
            'label' => $ll . 'tx_dmfcore_domain_model_test_case.serialized_expected_output',
            'config' => [
                'type' => 'user',
                'renderType' => JsonFieldElement::RENDER_TYPE,
                'cols' => 40,
                'rows' => 15,
            ],
        ],
    ],
];
