<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->exclude(
        [
        'app/Resources',
        'app/cache',
        'app/config',
        'app/logs',
        'web/bundles',
        'web/css',
        'web/js',
        'report-store',
        'src/Mesd/OrmedBundle/Resources/',
        'src/Mesd/OrmedBundle/DataFixtures',
        'vendor',
        ])
    ->in(__DIR__);

return Symfony\CS\Config\Config::create()
    ->level(Symfony\CS\FixerInterface::SYMFONY_LEVEL)
    ->fixers(
        [
        'align_double_arrow',
        'align_equals',
        'concat_with_spaces',
        'multiline_spaces_before_semicolon',
        'ordered_use',
        'short_array_syntax',
        ]
        )
    ->finder($finder);