<?php

declare(strict_types=1);

namespace App;

use App\Infrastructure\Dbal\Types\ProjectMetadataType;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    #[\Override]
    public function boot(): void
    {
        parent::boot();

        ProjectMetadataType::setSerializer($this->getContainer()->get('serializer_doctrine'));
    }
}
