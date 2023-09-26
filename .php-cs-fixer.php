<?php

$config = \TYPO3\CodingStandards\CsFixerConfig::create();
$config->setUsingCache(false);
$config->setRules([
    '@PSR12' => true,
    '@Symfony' => true,

    // modified rules
    'binary_operator_spaces' => ['operators' => ['=>' => null]],
    'class_definition' => ['single_item_single_line' => true],
    'global_namespace_import' => ['import_classes' => true, 'import_constants' => true, 'import_functions' => true],

    // disabled rules
    'no_superfluous_phpdoc_tags' => false, // conflicts with phpstan
    'nullable_type_declaration_for_default_null_value' => false,
    'phpdoc_align' => false,
    'phpdoc_summary' => false,
    'phpdoc_to_comment' => false, // conflicts with phpstan
    'yoda_style' => false,
]);
$config->getFinder()->in('Classes')->in('Configuration');

return $config;
