<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class Button
{
  public ?string $href = null;
  public ?string $target = null;

  public string $type = 'button';
  public string $variant = 'btn-primary';
  public string $label = '';

  public ?string $icon = null;
}
