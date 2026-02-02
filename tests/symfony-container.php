<?php

declare(strict_types=1);

use App\Shared\Infrastructure\Kernel;

require __DIR__.'/bootstrap.php';

$appKernel = new Kernel('test', false);
$appKernel->boot();

return $appKernel->getContainer();
