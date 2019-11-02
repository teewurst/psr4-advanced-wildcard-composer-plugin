<?php
declare(strict_types=1);

use teewurst\Prs4AdvancedWildcardComposer\Plugin;

return [
    Plugin::NAME => [
        'autoload' => [
            'psr-4' => [
                'My\\Namespace\\%s\\%s' => 'src/module/{*}/{*}/src'
            ]
        ],
        'autoload-dev' => [
            'psr-4' => [
                'My\\Namespace\\Test\\%s' => 'test/{*}/tests'
            ]
        ]
    ]
];
