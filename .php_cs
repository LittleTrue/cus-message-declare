<?php

return PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR2'               => true,
        '@Symfony'            => true,
        '@DoctrineAnnotation' => true,
        '@PhpCsFixer'         => true,
        'array_syntax' => [
            'syntax' => 'short',
        ],
        'list_syntax' => [
            'syntax' => 'short',
        ],
        'concat_space' => [
            'spacing' => 'one',
        ],
        'no_superfluous_phpdoc_tags' => [
            'allow_mixed' => false,
        ],
        'blank_line_before_statement' => [
            'statements' => [
                'declare',
            ],
        ],
        'binary_operator_spaces' => [
            'operators' => [
                '=>' => 'align_single_space_minimal',
                '='  => 'align_single_space_minimal',
            ],
        ],
        'general_phpdoc_annotation_remove' => [
            'annotations' => [
                'author',
            ],
        ],
        'list_syntax' => [
            'syntax' => 'short',
        ],
        'yoda_style' => [
            'always_move_variable' => true,
            'equal'                => true,
            'identical'            => true,
        ],
        'multiline_whitespace_before_semicolons' => [
            'strategy' => 'no_multi_line',
        ],
        'single_line_comment_style' => [
            'comment_types' => ['hash'],
        ],
        'class_attributes_separation'       => true,
        'combine_consecutive_unsets'        => true,
        'declare_strict_types'              => false,
        'linebreak_after_opening_tag'       => true,
        'lowercase_constants'               => true,
        'lowercase_static_reference'        => true,
        'no_useless_else'                   => true,
        'no_unused_imports'                 => true,
        'not_operator_with_successor_space' => false,
        'not_operator_with_space'           => false,
        'ordered_class_elements'            => true,
        'ordered_imports'                   => true,
        'php_unit_strict'                   => false,
        'phpdoc_separation'                 => false,
        'single_quote'                      => true,
        'standardize_not_equals'            => true,
         // case 空格
        'cast_spaces'                       => true,
         // else if  转换为 elseif
        'elseif'                            => true,
        // utf8 无bom头编码
        'encoding'                          => true,
        // 必须使用<?php 或者 <?=
        'full_opening_tag'                  => true,
        // 单行注释使用 双斜杠
        'hash_to_slash_comment'             => true,
         // class 大括号后不应有空行
        'no_blank_lines_after_class_opening'=> true,
        // 删除空注释
        'no_empty_comment'                  => true,
        // 在数组声明中，每个逗号之前绝对不能是空格
        'no_whitespace_before_comma_in_array'=> true,
        // 每个声明必须是一个使用关键字
        'single_import_per_statement'        => true,

    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->exclude('public')
            ->exclude('resources')
            ->exclude('config')
            ->exclude('runtime')
            ->exclude('vendor')
            ->in(__DIR__)
    )
    ->setUsingCache(false);
