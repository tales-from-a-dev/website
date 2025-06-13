<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/config',
        __DIR__.'/fixtures',
        __DIR__.'/src',
        __DIR__.'/tests',
    ])
    ->withPreparedSets(deadCode: true)
    ->withPhpSets(php84: true)
;
