<?php

namespace solvras\craftsvgtoolkit\services;

use Craft;
use craft\elements\Asset;
use craft\helpers\Html;
use craft\helpers\StringHelper;
use yii\base\Component;

/**
 * Svg Toolkit service
 */
class SvgToolkit extends Component
{
    /**
     * @param Asset|string $svg
     * @param array $options
     * @return string
     * Svg Toolkit
     */
    public function svgToolkit(Asset|string $svg, array $options = []): string
    {
        $resolvedSvg = $this->resolveSvgType($svg);
        $svgContents = Html::svg($resolvedSvg, true);
        // set options
        $class = $options['class'] ?? null;
        $color = $options['color'] ?? null;
        $title = $options['title'] ?? null;
        $replaceColor = $options['replaceColor'] ?? [];
        if ($svgContents) {
            // Add role=img to svg
            $svgContents = $this->addRoleImg($svgContents);
            // Add class to svg
            if ($class) {
                $svgContents = $this->addClass($svgContents, $class);
            }
            if ($color) {
                $svgContents = $this->replaceAllColors($svgContents, $color);
            }
            if ($title) {
                $svgContents = $this->setTitle($svgContents, $title);
            }
            foreach ($replaceColor as $oldColor => $newColor) {
                $svgContents = $this->replaceSpecificColor($svgContents, $oldColor, $newColor);
            }
            return $svgContents;
        } else {
            // return red warning icon if not svg
            return $this->warningSvg();
        }
    }


    /**
     * @param Asset|string $asset
     * @param string $color
     * @return string
     * Replace all colors in single colored svg
     */
    public function svgColor(Asset|string $asset, $color = 'currentColor'): string
    {
        if ($svg = Html::svg($asset, true, false)) {
            return $this->replaceAllColors($svg, $color);
        } else {
            // return red warning icon if not svg
            return $this->warningSvg();
        }
    }

    /**
     * @param Asset|string $asset
     * @param string $oldColor
     * @param string $newColor
     * @return string
     * Replace specific color in multicolored svg
     */
    public function svgReplaceColor(Asset|string $asset, $oldColor, $newColor): string
    {
        if ($svg = Html::svg($asset, true, false)) {
            return $this->replaceSpecificColor($svg, $oldColor, $newColor);
        } else {
            // return red warning icon if not svg
            return $this->warningSvg();
        }
    }

    /**
     * @param Asset|string $svgAsset
     * @param string $title
     * @return bool|string
     * Add title to svg
     */
    public function svgTitle(Asset|string $svgAsset, string $title): bool|string
    {
        if ($svg = Html::svg($svgAsset, true, false)) {
            return $this->setTitle($svg, $title);
        }
        return $this->warningSvg();
    }

    /**
     * @param string $svg
     * @param string $class
     * @return string
     * Add class to svg
     */
    public function addClass(string $svg, string $class): string
    {
        return Html::modifyTagAttributes($svg, ['class' => $class]);
    }

    /**
     * @param string $svg
     * @return string
     * Add role=img to svg
     */
    public function addRoleImg(string $svg): string
    {
        return Html::modifyTagAttributes($svg, ['role' => 'img']);
    }

    /**
     * @param string $svg
     * @return string
     * Remove xml declaration from svg
     */
    public function removeXmlDeclaration(string $svg): string
    {
        return preg_replace('/<\?xml.*\?>/i', '', $svg);
    }

    /**
     * @param string $svg
     * @param string $color
     * @return string
     * Replace all colors in single colored svg
     */
    public function replaceAllColors(string $svg, string $color): string
    {
        return preg_replace_callback('/(fill|stroke)="([0-9a-zA-Z()#]*)"/', function($matches) use ($color) {
            if ($matches[2] != 'none') {
                return $matches[1] . '="' . $color . '"';
            } else {
                return $matches[1] . '="none"';
            }
        }, $svg);
    }

    /**
     * @param string $svg
     * @param string $oldColor
     * @param string $newColor
     * @return string
     * Replace specific color in multicolored svg
     */
    public function replaceSpecificColor(string $svg, $oldColor, $newColor): string
    {
        return preg_replace_callback('/(fill|stroke)="(' . $oldColor . ')"/', function($matches) use ($newColor) {
            if ($matches[2] != 'none') {
                return $matches[1] . '="' . $newColor . '"';
            } else {
                return $matches[1] . '="none"';
            }
        }, $svg);
    }

    /**
     * @param string $svg
     * @param string $title
     * @return string
     * Set title to svg
     */
    public function setTitle(string $svg, string $title): string
    {
        $uniqueId = "svg" . StringHelper::toKebabCase($title) . uniqid();
        $svgDom = new \DOMDocument();
        $svgDom->loadXML($svg);
        // Check if title exists
        $titleElement = $svgDom->getElementsByTagName('title');
        if ($titleElement->length > 0) {
            $titleElement->item(0)->nodeValue = $title;
            $titleElement->item(0)->setAttribute('id', $uniqueId);
        } else {
            $titleElement = $svgDom->createElement('title', $title);
            $titleElement->setAttribute('id', $uniqueId);
            $svgDom->documentElement->insertBefore($titleElement, $svgDom->documentElement->firstChild);
        }
        $svgDom->documentElement->setAttribute('aria-labelledby', $uniqueId);
        return $svgDom->saveHTML();
    }

    /**
     * @param string $svgFileName
     * @return string|bool
     * Return svg markup from local files
     */
    public function svgFromLocalFilePath(string $svgFileName): string|bool
    {
        $config = Craft::$app->config->getConfigFromFile('svg-toolkit');
        if (array_key_exists('paths', $config)) {
            $paths = $config['paths'];
            foreach ($paths as $path) {
                // strip trailing slash of path and add slash before filename
                $path = rtrim($path, '/');
                $filePath = Craft::getAlias($path . "/" . $svgFileName . ".svg");
                if (file_exists($filePath)) {
                    return $filePath;
                }
            }
        }
        return false;
    }

    /**
     * @param Asset|string $svg
     * @return Asset|string
     * Resolve svg type
     */
    public function resolveSvgType(Asset|string $svg): Asset|string
    {
        // if Asset, return Asset
        if ($svg instanceof Asset) {
            return $svg;
        // if svg is a filapath or file name from local files
        } elseif (stripos($svg, '<svg') === false) {
            // if svg is a file path
            if (file_exists(Craft::getAlias($svg))) {
                return Craft::getAlias($svg);
            // if svg is a file name
            } else {
                return $this->svgFromLocalFilePath($svg);
            }
        } else {
            // if svg markup return svg markup, but clean it first
            return $this->removeXmlDeclaration($svg);
        }
    }

    /**
     * @return string
     * Return warning svg
     */
    public function warningSvg(): string
    {
        $uniqueId = uniqid();
        return '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ff0000" class="w-6 h-6" aria-labelledby="error' . $uniqueId . '" role="img">
                      <title id="error' . $uniqueId . '">WARNING</title>
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>';
    }
}
