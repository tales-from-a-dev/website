<?php

declare(strict_types=1);

namespace App\Ui\Twig\Component\Alert;

use App\Core\Enum\AlertType;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\UX\TwigComponent\Event\PreRenderEvent;

final class AlertComponentEventListener
{
    #[AsEventListener]
    public function onPreRender(PreRenderEvent $event): void
    {
        $component = $event->getComponent();
        if (!$component instanceof AlertComponent) {
            return;
        }

        if (AlertType::Toast === $component->alert->type) {
            $event->setTemplate('components/toast.html.twig');
        }
    }
}
