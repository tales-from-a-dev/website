<?php

declare(strict_types=1);

namespace App;

use App\Core\Doctrine\Dbal\Types\ProjectMetadataType;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function boot(): void
    {
        parent::boot();

        $this->addProjectMetadataTypeToDoctrine();
    }

    private function addProjectMetadataTypeToDoctrine(): void
    {
        if (
            ($serializer = $this->getContainer()->get('serializer_doctrine')) &&
            (
                $serializer instanceof NormalizerInterface &&
                $serializer instanceof DenormalizerInterface
            )
        ) {
            ProjectMetadataType::setSerializer($serializer);
        }
    }
}
