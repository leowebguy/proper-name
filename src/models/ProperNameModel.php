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

namespace leowebguy\propername\models;

use craft\base\Model;
use craft\validators\ArrayValidator;

/**
 * Class ProperNameModel
 */
class ProperNameModel extends Model
{
    public $wordList = []; //'black','asian','african','indian','caucasian','woman','men','women','men','chinese','american','mexican','elder','young'

    public $cacheTime = 24;

    public function rules()
    {
        $rules = [
            [['wordList'], ArrayValidator::class]
        ];

        return $rules;
    }
}
