<?php

namespace solvras\craftsvgtoolkit\web\twig;

use craft\elements\Asset;
use solvras\craftsvgtoolkit\SvgToolkit;
use Twig\Extension\AbstractExtension;
use Twig\Markup;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Twig extension
 */
class Extension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('svgToolkit', [$this, 'svgToolkit' ], ['is_safe' => ['html']]),
        ];
    }

    public function getFilters(): array
    {
        return  [
            new TwigFilter('svgColor', [$this, 'svgColorFilter' ], ['is_safe' => ['html']]),
            new TwigFilter('svgTitle', [$this, 'svgTitleFilter' ], ['is_safe' => ['html']]),
        ];
    }
    /**
     * Twig functions
     */

    public function svgToolkit(Asset|string $svg, $class = ""): string
    {
        return new Markup(SvgToolkit::getInstance()->svgToolkit->svgToolkit($svg, $class), 'utf-8');
    }


    /**
     * Twig filters
     */
    public function svgColorFilter(Asset|string $asset, $color = 'currentColor'): string
    {
        return SvgToolkit::getInstance()->svgToolkit->svgColor($asset, $color);
    }

    public function svgTitleFilter(Asset|string $svgAsset, string $title): string
    {
        return SvgToolkit::getInstance()->svgToolkit->svgTitle($svgAsset, $title);
    }

}
