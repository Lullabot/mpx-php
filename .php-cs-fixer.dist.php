<?php

return (new PhpCsFixer\Config())
    ->setRules(array(
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'array_syntax' => array('syntax' => 'short'),
        'no_unreachable_default_argument_value' => false,
        'heredoc_to_nowdoc' => false,
        'phpdoc_annotation_without_dot' => false,
        'fully_qualified_strict_types' => array(
            'phpdoc_tags' => array('param', 'return', 'throws', 'see'),
        ),
    ))
    ->setRiskyAllowed(true)
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(__DIR__.'/src')
            ->in(__DIR__.'/tests')
    )
;
