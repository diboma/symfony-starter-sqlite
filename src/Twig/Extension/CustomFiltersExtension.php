<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\CustomFiltersRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class CustomFiltersExtension extends AbstractExtension
{
  public function getFilters(): array
  {
    return [
      // If your filter generates SAFE HTML, you should add a third
      // parameter: ['is_safe' => ['html']]
      // Reference: https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
      new TwigFilter('contains', [$this, 'contains']),
    ];
  }

  public function contains(string $haystack, string $needle): bool
  {
    return str_contains($haystack, $needle);
  }

  public function getFunctions(): array
  {
    return [
      new TwigFunction('contains', [$this, 'contains']),
    ];
  }
}
