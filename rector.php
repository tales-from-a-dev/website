<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Zenstruck\Foundry\Utils\Rector\FoundrySetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/config',
        __DIR__.'/fixtures',
        __DIR__.'/src',
        __DIR__.'/tests',
    ])
    ->withPreparedSets(deadCode: true)
    ->withPhpSets(php84: true)
    ->withSets([
        FoundrySetList::UP_TO_FOUNDRY_2,
    ])
;
