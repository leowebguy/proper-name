<?php
/**
 * Proper Name plugin for Craft CMS 3.x
 * This plugin reduces liability and improves SEO by preventing biased (gender, ethnicity...),
 * copyrighted (shutterstock, getty...) and other not desired/recommended assets naming.
 *
 * @author     Leo Leoncio
 * @see        https://github.com/leowebguy
 * @copyright  Copyright (c) 2021, leowebguy
 * @license    MIT
 */

namespace leowebguy\propername;

use leowebguy\propername\models\ProperNameModel;

use Craft;
use craft\base\Plugin;
use craft\elements\Entry;
use craft\events\ModelEvent;
use craft\base\Model;
use craft\events\PluginEvent;
use craft\helpers\UrlHelper;
use craft\services\Plugins;
use yii\base\Event;
use craft\elements\db\AssetQuery;

/**
 * Class ProperName
 */
class ProperName extends Plugin
{
    // Properties
    // =========================================================================
    public static $plugin;

    // Public Methods
    // =========================================================================
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        if (!$this->isInstalled) {
            return;
        }

        // after install
        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                    Craft::$app->getResponse()->redirect(
                        UrlHelper::cpUrl('settings/plugins/proper-name', [])
                    )->send();
                }
            }
        );

        // before save
        Event::on(
            Entry::class,
            Entry::EVENT_BEFORE_SAVE,
            function (ModelEvent $event) {
                $result = [];
                foreach ($event->sender->getFieldValues() as $key => $field) {
                    if ($field instanceof AssetQuery) {
                        foreach ($field->all() as $asset) {
                            $result[$key][] = self::$plugin->propernameService->matchName($asset->filename);
                        }
                    }
                }
                if (!empty($result)) {
                    $errors = [];
                    foreach ($result as $field => $value) {
                        foreach ($value as $asset) {
                            foreach ($asset as $match) {
                                $errors[] = $match;
                            }
                        }
                        $event->sender->addError($field, 'Assets contain these NOT recommended ' .
                            'words: ' . implode(', ', $errors) . '. Please rename it and try again.');
                    }
                    return $event->isValid = false;
                }
            }
        );

        // log info
        Craft::info(
            'Proper Name plugin loaded',
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================
    protected function createSettingsModel(): ?Model
    {
        return new ProperNameModel();
    }

    protected function settingsHtml(): ?string
    {
        return Craft::$app->getView()->renderTemplate(
            'proper-name/settings',
            ['settings' => $this->getSettings()]
        );
    }
}
