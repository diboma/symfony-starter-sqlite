<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class Avatar
{
    public string $avatarUrl = '';
    public string $alt = 'avatar';
    public string $username = 'John Doe';
    public string $size = 'small';
}
