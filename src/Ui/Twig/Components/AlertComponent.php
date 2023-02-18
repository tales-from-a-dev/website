<?php

declare(strict_types=1);

namespace App\Ui\Twig\Components;

use App\Core\Model\Alert;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('alert')]
final class AlertComponent
{
    public Alert $alert;
}
