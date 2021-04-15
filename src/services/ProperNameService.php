<?php
/**
 * Proper Name plugin for Craft CMS 3.x
 * This plugin reduces liability and improves SEO by preventing biased (gender, ethnicity...),
 * copyrighted (shutterstock, getty...) and other not desired/recommended assets naming
 *
 * @author     Leo Leoncio
 * @link       https://github.com/leowebguy
 * @copyright  Copyright (c) 2021, leowebguy
 * @license    MIT
 */

namespace leowebguy\propername\services;

use Craft;
use craft\base\Component;

/**
 * Class ProperNameService
 */
class ProperNameService extends Component
{
    public function matchName($filename)
    {
        $list = implode('|', $this->getList());

        preg_match_all('/' . $list . '/mi', $filename, $matches);

        if (!empty($matches[0])) {
            return $matches[0];
        }

        return [];
    }

    private function getList()
    {
        $list = Craft::$app->cache->get('propername_list') ?? [];

        if (empty($list)) {
            $settings = Craft::$app->plugins->getPlugin('proper-name')->getSettings();
            $full_list = $settings['wordList'];

            foreach ($full_list as $value) {
                $list[] = $value[0];
            }

            Craft::$app->cache->set('propername_list', $list, 60 * 60 * ((int)$settings['cacheTime']));
        }

        return $list;
    }
}
