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
            new TwigFilter('svgReplaceColor', [$this, 'svgReplaceColorFilter' ], ['is_safe' => ['html']]),
            new TwigFilter('svgTitle', [$this, 'svgTitleFilter' ], ['is_safe' => ['html']]),
        ];
    }
    /**
     * Twig functions
     */

    /**
     * @param Asset|string $svg
     * @param array $options
     * @return string
     * Svg Toolkit
     */
    public function svgToolkit(Asset|string $svg, array $options = []): string
    {
        return new Markup(SvgToolkit::getInstance()->svgToolkit->svgToolkit($svg, $options), 'utf-8');
    }

    /**
     * Twig filters
     */

    /**
     * @param Asset|string $asset
     * @param string $color
     * @return string
     * Svg Color
     */
    public function svgColorFilter(Asset|string $asset, string $color = 'currentColor'): string
    {
        return SvgToolkit::getInstance()->svgToolkit->svgColor($asset, $color);
    }

    /**
     * @param Asset|string $asset
     * @param string $color
     * @param string $replaceColor
     * @return string
     * Svg Replace Color
     */
    public function svgReplaceColorFilter(Asset|string $asset, string $color, string $replaceColor): string
    {
        return SvgToolkit::getInstance()->svgToolkit->svgReplaceColor($asset, $color, $replaceColor);
    }

    /**
     * @param Asset|string $svgAsset
     * @param string $title
     * @return string
     * Svg Title
     */
    public function svgTitleFilter(Asset|string $svgAsset, string $title): string
    {
        return SvgToolkit::getInstance()->svgToolkit->svgTitle($svgAsset, $title);
    }
}
