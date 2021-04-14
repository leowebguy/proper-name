<?php
/**
 * Proper Name plugin for Craft CMS 3.x
 * This plugin reduces liability and improves SEO by preventing biased (gender, ethnicity...) and/or copyrighted (shutterstock, getty...) assets naming
 *
 * @author     Leo Leoncio
 * @link       https://github.com/leowebguy
 * @copyright  Copyright (c) 2021, leowebguy
 * @license    MIT
 */

namespace leowebguy\propername;

use Craft;
use craft\base\Plugin;
use craft\elements\Asset;
use craft\events\ModelEvent;
use craft\events\ReplaceAssetEvent;
use craft\services\Assets;
use leowebguy\propername\models\ProperNameModel;
use yii\base\Event;

/**
 * Class ProperName
 */
class ProperName extends Plugin
{
    public static $plugin;

    public function init()
    {
        parent::init();
        self::$plugin = $this;

        // new asset
        Event::on(
            Asset::class,
            Asset::EVENT_BEFORE_SAVE,
            function(ModelEvent $event) {
                if ($event->isNew && $event->sender instanceof Asset) {
                    $result = self::$plugin->propernameService->matchName($event->sender->filename);
                    if (!empty($result)) {
                        $event->sender->addError('filename', Craft::t('proper-name', 'The asset ' . $event->sender->filename . ' has ' .
                            'NOT recommended words: ' . implode(', ', $result) . ', please rename it and try again.'));

                        return $event->isValid = false;
                    }
                }
            }
        );

        // replace asset
        Event::on(
            Assets::class,
            Assets::EVENT_BEFORE_REPLACE_ASSET,
            function(ReplaceAssetEvent $event) {
                if ($event->asset instanceof Asset) {
                    $result = self::$plugin->propernameService->matchName($event->asset->filename);
                    if (!empty($result)) {
                        $event->asset->addError('filename', Craft::t('proper-name', 'The asset ' . $event->asset->filename . ' has ' .
                            'NOT recommended words: ' . implode(', ', $result) . ', please rename it and try again.'));

                        return $event->isValid = false;
                    }
                }
            }
        );

        // log info
        Craft::info(
            Craft::t(
                'proper-name',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    protected function createSettingsModel()
    {
        return new ProperNameModel();
    }

    protected function settingsHtml()
    {
        return Craft::$app->getView()->renderTemplate(
            'proper-name/settings',
            ['settings' => $this->getSettings()]
        );
    }
}
