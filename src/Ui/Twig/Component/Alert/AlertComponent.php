<?php

declare(strict_types=1);

namespace App\Ui\Twig\Component\Alert;

use App\Core\Model\Alert;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'alert')]
final class AlertComponent
{
    public Alert $alert;
}
