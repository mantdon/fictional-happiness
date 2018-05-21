<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class HighlightPatternExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('highlight_pattern', [$this, 'highlight'], ['is_safe' => ['html']]),
        ];
    }

    public function highlight(?string $value, ?string $pattern, string $color = '')
    {
        if ($value === null || $pattern === null || strlen($pattern) === 0) {
            return $value;
        }

        $replaceWith = '<mark style="padding: 0 0 0 0; background-color: ' . $color . '">$0</mark>';
        return preg_replace("/" . preg_quote($pattern) . "/i", $replaceWith, $value);
    }
}
