<?php
/**
 * Enlight
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://enlight.de/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@shopware.de so we can send you a copy immediately.
 *
 * @category   Enlight
 * @package    Enlight_Tests
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license/new-bsd     New BSD License
 * @version    $Id$
 * @author     Heiner Lohaus
 * @author     $Author$
 */

/**
 * Test case
 *
 * @category   Enlight
 * @package    Enlight_Tests
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license/new-bsd     New BSD License
 */
class Enlight_Tests_Controller_View_CacheTest extends Enlight_Components_Test_Controller_TestCase
{
    /**
     * @var Enlight_Template_Manager
     */
    protected $engine;

    /**
     * Sets up
     */
	public function setUp()
    {
        $tempDir = Enlight_TestHelper::Instance()->TestPath('TempFiles');

        $this->engine = new Enlight_Template_Manager();
        $this->engine->setCompileDir($tempDir);
        $this->engine->setCacheDir($tempDir);
        $this->engine->setCompileId('cache');
        $this->engine->clearCompiledTemplate(null, 'cache');
        $this->engine->clearCache(null, null, 'cache');

        Smarty::$global_tpl_vars = array();
	}

    /**
     * Test case
     */
    public function testViewSetCacheId()
    {
        $view = new Enlight_View_Default($this->engine);
        $view->loadTemplate('string:');

        $view->setCacheId('test');
        $this->assertEquals('test', $view->getCacheId());

        $view->setCacheId(array('test', '2'));
        $this->assertEquals('test|2', $view->getCacheId());
    }

    /**
     * Test case
     */
    public function testViewAddCacheId()
    {
        $view = new Enlight_View_Default($this->engine);
        $view->loadTemplate('string:');

        $view->setCacheId('test');
        $this->assertEquals('test', $view->getCacheId());

        $view->addCacheId('2');
        $this->assertEquals('test|2', $view->getCacheId());
    }

    /**
     * Test case
     */
    public function testViewCache()
    {
        $view = new Enlight_View_Default($this->engine);
        $view->loadTemplate('string:hello {uniqid()}');
        $view->setCaching(true);
        $this->assertFalse($view->isCached());

        $result = $view->render();

        $view->loadTemplate('string:hello {uniqid()}');
        $view->setCaching(true);
        $this->assertTrue($view->isCached());
        $this->assertEquals($result, $view->render());
    }
}