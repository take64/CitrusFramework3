<?php

declare(strict_types=1);

return [
    'preset' => 'default',
    'exclude' => [
        //  'path/to/directory-or-file'
    ],
    'add' => [
        //  ExampleMetric::class => [
        //      ExampleInsight::class,
        //  ]
        NunoMaduro\PhpInsights\Domain\Metrics\Code\Code::class => [
            SlevomatCodingStandard\Sniffs\ControlStructures\RequireYodaComparisonSniff::class,
        ],
    ],
    'remove' => [
        //  ExampleInsight::class,
        SlevomatCodingStandard\Sniffs\ControlStructures\DisallowShortTernaryOperatorSniff::class,
        SlevomatCodingStandard\Sniffs\ControlStructures\DisallowYodaComparisonSniff::class,
        SlevomatCodingStandard\Sniffs\PHP\UselessParenthesesSniff::class,
        SlevomatCodingStandard\Sniffs\TypeHints\DisallowArrayTypeHintSyntaxSniff::class,
        SlevomatCodingStandard\Sniffs\TypeHints\UselessConstantTypeHintSniff::class,
        PhpCsFixer\Fixer\ControlStructure\NoUnneededControlParenthesesFixer::class,
        PhpCsFixer\Fixer\Operator\TernaryOperatorSpacesFixer::class,
        PhpCsFixer\Fixer\Phpdoc\PhpdocInlineTagFixer::class,
        PhpCsFixer\Fixer\Phpdoc\PhpdocSeparationFixer::class,
    ],
    'config' => [
        //  ExampleInsight::class => [
        //      'key' => 'value',
        //  ],
        ObjectCalisthenics\Sniffs\NamingConventions\ElementNameMinimalLengthSniff::class => [
            'minLength' => 3,
            'allowedShortNames' => ['i', 'id', 'to', 'up', 'ky', 'vl'],
        ],
    ],
];
