<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Seo;

use App\Shared\Domain\Enum\SharedRouteNameEnum;
use Symfony\Component\Asset\Packages;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\UrlHelper;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class HomepageStructuredDataBuilder
{
    /**
     * @param list<array{name: string, url: string}> $socialAccounts
     */
    public function __construct(
        #[Autowire(value: '%app.contact_email%')] private string $contactEmail,
        #[Autowire(value: '%app.social_accounts%')] private array $socialAccounts,
        private Packages $assetPackages,
        private UrlHelper $urlHelper,
        private TranslatorInterface $translator,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function build(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Person',
            'name' => $this->translator->trans('app.author'),
            'jobTitle' => $this->translator->trans('app.job_title'),
            'url' => $this->urlGenerator->generate(SharedRouteNameEnum::WebsiteIndex->value, [], UrlGeneratorInterface::ABSOLUTE_URL),
            'image' => $this->urlHelper->getAbsoluteUrl($this->assetPackages->getUrl('images/profile_md.webp')),
            'email' => 'mailto:'.$this->contactEmail,
            'sameAs' => array_column($this->socialAccounts, 'url'),
        ];
    }
}
