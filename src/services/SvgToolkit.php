<?php

namespace solvras\craftsvgtoolkit\services;

use Craft;
use craft\elements\Asset;
use craft\helpers\Html;
use craft\helpers\StringHelper;
use craft\web\twig\Extension;
use yii\base\Component;
use yii\base\InvalidArgumentException;

/**
 * Svg Toolkit service
 */
class SvgToolkit extends Component
{

    public function svgFromLocalFilePath(string $svgFileName): string|bool {
        $config = Craft::$app->config->getConfigFromFile('svg-toolkit');
        if(array_key_exists('paths', $config)) {
            $paths = $config['paths'];
            foreach($paths as $path) {
                // @TODO fix trailing slash from config
                $filePath = Craft::getAlias($path."/".$svgFileName.".svg");
                if(file_exists($filePath)) {
                    return $filePath;
                }
            }
        }
        return false;
    }
    public function svgToolkit(Asset|string $svg, $class = ""): string {

        $resolvedSvg = $this->resolveSvgType($svg);
        $svgContents = Html::svg($resolvedSvg, true);
        if($svgContents) {
            try {
                $svgContents =  Html::modifyTagAttributes($svgContents, ['role' => 'img']);
                $svgContents =  Html::modifyTagAttributes($svgContents, ['class' => $class]);
            } catch (InvalidArgumentException $e) {
                Craft::warning($e->getMessage(), __METHOD__);
            }
            return $svgContents;
        } else {
            // return red warning icon if not svg
            return $this->warningSvg();
        }
    }

    public function resolveSvgType(Asset|string $svg): Asset|string {
        // if Asset, return Asset
        if($svg instanceof Asset) {
            return $svg;
            // if svg is a filapath or file name from local files
        } elseif(stripos($svg, '<svg') === false) {
            if(file_exists(Craft::getAlias($svg))) {
                return Craft::getAlias($svg);
            } else {
                return $this->svgFromLocalFilePath($svg);
            }
        } else {
            // if svg markup return svg markup, but clean it first
            return preg_replace('/<\?xml.*\?>/i', '', $svg);
        }
    }

    public function svgColor(Asset|string $asset, $color = 'currentColor'): string {

        if($svg = Html::svg($asset, true, false)) {
            return preg_replace_callback('/(fill|stroke)="([0-9a-zA-Z()#]*)"/', function($matches) use ($color){
                if($matches[2] != 'none') {
                    return $matches[1] . '="'.$color.'"';
                } else {
                    return $matches[1] . '="none"';
                }
            }, $svg);
        } else {
            // return red warning icon if not svg
            return $this->warningSvg();
        }
    }

    public function svgTitle(Asset|string $svgAsset, string $title): bool|string {
        if($svg = Html::svg($svgAsset, true, false)) {
            $uniqueId = "svg".StringHelper::toKebabCase($title).uniqid();
            $svgDom  = new \DOMDocument();
            $svgDom->loadXML($svg);
            // Check if title exists
            $titleElement = $svgDom->getElementsByTagName('title');
            if($titleElement->length > 0) {
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
        return false;
    }

    /**
     * Helper functions
     */
    private function warningSvg(): string {
        $uniqueId = uniqid();
        return '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ff0000" class="w-6 h-6" aria-labelledby="error'.$uniqueId.'" role="img">
                      <title id="error'.$uniqueId.'">WARNING</title>
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>';
    }
}
