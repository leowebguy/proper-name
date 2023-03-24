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

namespace leowebguy\propername\models;

use craft\base\Model;
use craft\validators\ArrayValidator;

class ProperModel extends Model
{
    // Properties
    // =========================================================================

    public array $wordList = [];

    public int $cacheTime = 24;

    // Public Methods
    // =========================================================================

    public function rules(): array
    {
        return [
            [['wordList'], ArrayValidator::class],
        ];
    }
}
