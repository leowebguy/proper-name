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

namespace leowebguy\propername\migrations;

use Craft;
use craft\db\Migration;
use leowebguy\propername\ProperName;

class Install extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        Craft::$app->getPlugins()->savePluginSettings(
            ProperName::$plugin,
            [
                'wordList' => [
                    ['asian'], ['african'], ['shutterstock'], ['getty'], ['young'], ['elder'], ['woman']
                ],
                'cacheTime' => '12'
            ]
        );
        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        return true;
    }
}
