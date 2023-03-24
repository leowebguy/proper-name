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

namespace leowebguy\propername\services;

use Craft;
use craft\base\Component;

/**
 * @property-read array $list
 */
class ProperService extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * @param $filename
     * @return array
     */
    public function matchName($filename): array
    {
        $list = implode('|', $this->getList());
        if (!empty($list)) {
            preg_match_all('/' . $list . '/mi', $filename, $matches);
            if (!empty($matches[0])) {
                return $matches[0];
            }
        }
        return [];
    }

    // Private Methods
    // =========================================================================

    /**
     * @return array|mixed
     */
    private function getList(): mixed
    {
        $list = Craft::$app->cache->get('propername_list') ?: [];

        // Empty cache
        if (empty($list)) {
            $settings = Craft::$app->plugins->getPlugin('proper-name')->getSettings();
            $full_list = $settings['wordList'];

            // Empty settings > wordList
            if (empty($full_list)) {
                return [];
            }
            foreach ($full_list as $value) {
                if (!empty($value[0])) {
                    $list[] = $value[0];
                }
            }
            Craft::$app->cache->set('propername_list', $list, 60 * 60 * ((int)$settings['cacheTime']));
        }
        return $list;
    }
}
