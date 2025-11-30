<?php

use PhpCsFixer\Finder;
use PhpCsFixer\Config;

$finder = Finder::create()
    ->in(__DIR__)
    ->exclude(['node_modules', 'vendor', 'storage', 'public/build'])
    ->name('*.php')
;

$config = new Config();

return $config
    ->setRules([
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'trailing_comma_in_multiline' => ['elements' => ['arrays']],
        'no_unused_imports' => true,
        'binary_operator_spaces' => ['default' => 'align_single_space_minimal'],
        'nullable_type_declaration' => true,
    ])
    ->setFinder($finder)
    ->setRiskyAllowed(true)
;
