<?php

declare(strict_types=1);

$rules = [
    '@PER-CS2.0' => true,
    '@PHP83Migration' => true,
    'array_push' => true,
    'mb_str_functions' => true,
    'no_multiline_whitespace_around_double_arrow' => true,
    'whitespace_after_comma_in_array' => true,
    'modernize_types_casting' => true,
    'explicit_string_variable' => true,
    'ordered_types' => [
        'case_sensitive' => true,
        'sort_algorithm' => 'none',
        'null_adjustment' => 'always_last',
    ],
    'no_alternative_syntax' => true,
    'native_function_invocation' => [
        'include' => ['@internal'],
        'scope' => 'all',
        'strict' => true,
    ],
    'global_namespace_import' => [
        'import_classes' => false,
        'import_constants' => true,
        'import_functions' => true,
    ],
    'no_unused_imports' => true,
    'no_homoglyph_names' => true,
    'assign_null_coalescing_to_coalesce_equal' => true,
    'ternary_to_null_coalescing' => true,
    'unary_operator_spaces' => true,
    'long_to_shorthand_operator' => true,
    'strict_comparison' => true,
    'braces_position' => [
        'anonymous_classes_opening_brace' => 'next_line_unless_newline_at_signature_end',
        'anonymous_functions_opening_brace' => 'next_line_unless_newline_at_signature_end',
    ],
    'declare_strict_types' => true,
    'strict_param' => true,
    'no_empty_comment' => true,
    'no_empty_phpdoc' => true,
    'yoda_style' => true,
    'final_class' => true,
];

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules($rules)
    ->setFinder((new PhpCsFixer\Finder())->in(__DIR__ . '/src'));
