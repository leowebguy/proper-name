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

namespace leowebguy\propername\tests\unit;

use Codeception\Test\Unit;
use leowebguy\propername\ProperName;
use UnitTester;

//use Craft;
//use craft\helpers\App;
//use Codeception\Util\Debug;

class ProperNameTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    protected function _before()
    {
        parent::_before();
    }

    protected function _after()
    {
        parent::_after();
    }

    // Public methods
    // =========================================================================
    public function testProperNameInstance(): void
    {
        self::assertInstanceOf(ProperName::class, ProperName::$plugin);
    }

    public function testProperNameServiceMatchName(): void
    {
        $result = ProperName::getInstance()->propernameService->matchName('asset-shutterstock-047682.jpg');
        self::assertArrayHasKey(0, $result); // match shutterstock
    }

    public function testProperNameGetSettings(): void
    {
        $result = ProperName::getInstance()->getSettings();
        self::assertArrayHasKey('wordList', $result); // has wordList key
        self::assertArrayHasKey('cacheTime', $result); // has cacheTime key
    }

    public function testProperNameWordListHasData(): void
    {
        $result = ProperName::getInstance()->getSettings()['wordList'];
        self::assertIsArray($result); // wordList is array
        self::assertArrayHasKey(0, $result); // has at least one entry
    }

//    public function testSiteGetStatusCode()
//    {
//        $client = Craft::createGuzzleClient();
//        $response = $client->request('GET', 'http://' . App::env('SITE_URL') . '/');
//        self::assertEquals($response->getStatusCode(), '200');
//    }
}
