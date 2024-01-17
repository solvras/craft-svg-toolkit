<?php

namespace solvras\craftsvgtoolkit;

use Craft;
use craft\base\Model;
use craft\base\Plugin;
use solvras\craftsvgtoolkit\models\Settings;
use solvras\craftsvgtoolkit\services\SvgToolkit as SvgToolkitAlias;
use solvras\craftsvgtoolkit\web\twig\Extension;

/**
 * svg-toolkit plugin
 *
 * @method static SvgToolkit getInstance()
 * @method Settings getSettings()
 * @author Solvras <support@solvr.no>
 * @copyright Solvras
 * @license https://craftcms.github.io/license/ Craft License
 * @property-read SvgToolkitAlias $svgToolkit
 */
class SvgToolkit extends Plugin
{
    public string $schemaVersion = '1.0.0';
    public bool $hasCpSettings = false;



    public function init(): void
    {
        parent::init();

        $this->setComponents([
            'svgToolkit' => SvgToolkitAlias::class,
        ]);

        // Add svg as a separate file kind
        Craft::$app->config->general->extraFileKinds = [
            'svg' => [
                'label' => 'SVG',
                'extensions' => ['svg'],
            ],
        ];

        // Defer most setup tasks until Craft is fully initialized
        Craft::$app->onInit(function() {
            $this->attachEventHandlers();
            // ...
        });
        Craft::$app->view->registerTwigExtension(new Extension());
    }

    protected function createSettingsModel(): ?Model
    {
        return Craft::createObject(Settings::class);
    }

    private function attachEventHandlers(): void
    {
        // Register event handlers here ...
        // (see https://craftcms.com/docs/4.x/extend/events.html to get started)
    }
}
