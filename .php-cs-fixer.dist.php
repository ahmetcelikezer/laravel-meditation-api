<?php

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__.'/app',
        __DIR__.'/config',
        __DIR__.'/routes',
        __DIR__.'/database',
        __DIR__.'/tests',
    ])
    ->exclude([
        __DIR__.'/storage',
        __DIR__.'/.php-cs-fixer.dist.php',
    ])
    ->ignoreDotFiles(true)
    ->name('*.php')
;

$config = new PhpCsFixer\Config();

$config->setRiskyAllowed(true);
$config->setFinder($finder);
$config->setRules([
    '@PhpCsFixer' => true,
]);
$config->setUsingCache(true);

return $config;
