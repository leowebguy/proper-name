<?php
/**
 * Proper Name plugin for Craft CMS
 *
 * This plugin reduces liability and improves SEO by preventing biased (gender, ethnicity...),
 * copyrighted (shutterstock, getty...) and other non desired naming.
 *
 * @author     Leo Leoncio
 * @see        https://github.com/leowebguy
 * @copyright  Copyright (c) 2023, leowebguy
 * @license    MIT
 */

namespace leowebguy\propername;

use Craft;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use craft\base\Element;
use craft\base\Model;
use craft\base\Plugin;
use craft\elements\Entry;
use craft\elements\db\AssetQuery;
use craft\events\ModelEvent;
use craft\events\PluginEvent;
use craft\helpers\UrlHelper;
use craft\services\Plugins;
use leowebguy\propername\models\ProperModel;
use leowebguy\propername\services\ProperService;
use yii\base\Event;
use yii\base\Exception;

class ProperName extends Plugin
{
    // Properties
    // =========================================================================

    public static $plugin;

    public bool $hasCpSection = false;

    public bool $hasCpSettings = true;

    // Public Methods
    // =========================================================================

    public function init()
    {
        parent::init();
        self::$plugin = $this;

        if (!$this->isInstalled) {
            return;
        }

        $this->setComponents([
            'properService' => ProperService::class
        ]);

        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function(PluginEvent $event) {
                if ($event->plugin === $this) {
                    Craft::$app->getResponse()->redirect(
                        UrlHelper::cpUrl('settings/plugins/proper-name', [])
                    )->send();
                }
            }
        );

        Event::on(
            Entry::class,
            Element::EVENT_BEFORE_SAVE,
            function(ModelEvent $event) {
                $result = [];
                foreach ($event->sender->getFieldValues() as $key => $field) {
                    if ($field instanceof AssetQuery) {
                        foreach ($field->all() as $asset) {
                            $result[$key][] = self::$plugin->properService->matchName($asset->filename);
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
                return $event->isValid = true;
            }
        );

        Craft::info(
            'Proper Name plugin loaded',
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================

    /**
     * @return Model|null
     */
    protected function createSettingsModel(): ?Model
    {
        return new ProperModel();
    }

    /**
     * @return string|null
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
    protected function settingsHtml(): ?string
    {
        return Craft::$app->getView()->renderTemplate(
            'proper-name/settings',
            ['settings' => $this->getSettings()]
        );
    }
}
