<?php

declare(strict_types=1);

namespace App\Ui\Controller;

use App\Domain\Enum\AlertStatusEnum;
use App\Domain\Model\Alert;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as SymfonyAbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatableMessage;

abstract class AbstractController extends SymfonyAbstractController
{
    /**
     * @param array<string, mixed> $parameters
     */
    protected function redirectToReferrer(string $fallbackRoute, array $parameters = [], int $status = Response::HTTP_FOUND): RedirectResponse
    {
        if (null !== $request = $this->container->get('request_stack')->getCurrentRequest()) {
            $referrer = $request->headers->get('referer');
        }

        return $this->redirectToRoute($referrer ?? $fallbackRoute, $parameters, $status);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    protected function addAlert(AlertStatusEnum $status, string|TranslatableMessage $message, array $parameters = []): void
    {
        if (\is_string($message)) {
            $message = new TranslatableMessage($message, $parameters, 'alert');
        }

        $this->addFlash('alert', new Alert($message, $status));
    }
}
