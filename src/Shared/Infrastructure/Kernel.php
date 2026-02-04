<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure;

use App\Shared\Infrastructure\Dbal\Types\ProjectMetadataType;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function boot(): void
    {
        parent::boot();

        ProjectMetadataType::setSerializer($this->getContainer()->get('serializer_doctrine'));
    }

    public function getShareDir(): string
    {
        return $this->getProjectDir().'/var/share/'.$this->environment;
    }

    protected function configureContainer(ContainerConfigurator $container, LoaderInterface $loader, ContainerBuilder $builder): void
    {
        $configDir = $this->getConfigDir();

        $container->import(\sprintf('%s/{packages}/*.{php,yaml}', $configDir));
        $container->import(\sprintf('%s/{packages}/%s/*.{php,yaml}', $configDir, $this->environment));

        $container->import(\sprintf('%s/{services}/*.{php,yaml}', $configDir));
        $container->import(\sprintf('%s/{services}/%s/*.{php,yaml}', $configDir, $this->environment));
    }
}
