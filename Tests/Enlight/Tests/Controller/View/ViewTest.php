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
 * @author     h.lohaus
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
class Enlight_Tests_Controller_View_ViewTest extends Enlight_Components_Test_Controller_TestCase
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
        $this->engine->setCompileId('snippet');
        $this->engine->clearCompiledTemplate(null, 'snippet');

        Smarty::$global_tpl_vars = array();
	}

    /**
     * Test case
     */
    public function testViewInit()
    {
        $view = new Enlight_View_Default($this->engine);
        $this->assertEquals($this->engine, $view->Engine());
    }

    /**
     * Test case
     */
    public function testViewRenderTemplate()
    {
        $view = new Enlight_View_Default($this->engine);
        $view->loadTemplate('string:test');
        $this->assertEquals('test', $view->render());
    }

    /**
     * Test case
     */
    public function testViewHasTemplate()
    {
        $view = new Enlight_View_Default($this->engine);
        $view->loadTemplate('string:');
        $this->assertEquals('test', $view->hasTemplate());
    }

    /**
     * Test case
     */
    public function testViewAssign()
    {
        $view = new Enlight_View_Default($this->engine);
        $view->loadTemplate('string:{$test}');
        $view->assign('test', 'success');
        $this->assertEquals('success', $view->render());
    }

    /**
     * Test case
     */
    public function testViewAssignGlobal()
    {
        $view = new Enlight_View_Default($this->engine);
        $view->loadTemplate('string:{$test}');
        $view->assign('test', 'success', null, Smarty::SCOPE_GLOBAL);
        $this->assertEquals('success', $view->getAssign('test'));

        $view = new Enlight_View_Default($this->engine);
        $view->loadTemplate('string:{$test}');
        $this->assertEquals('success', $view->getAssign('test'));
    }

    /**
     * Test case
     */
    public function testViewAssignRoot()
    {
        $view = new Enlight_View_Default($this->engine);
        $view->loadTemplate('string:{$test}');
        $view->assign('test', 'success', null, Smarty::SCOPE_ROOT);
        $this->assertEquals('success', $view->getAssign('test'));

        $view = new Enlight_View_Default($this->engine);
        $view->loadTemplate('string:{$test}');
        $this->assertEquals('success', $view->getAssign('test'));
    }

    /**
     * Test case
     */
    public function testViewAssignRoot2()
    {
        $view = new Enlight_View_Default($this->engine);
        $view->loadTemplate('string:{$test}');
        $view->assign('test', 'success', null, Smarty::SCOPE_ROOT);
        $this->assertEquals('success', $view->getAssign('test'));

        $view = new Enlight_View_Default($this->engine);
        $view->loadTemplate('string:{$test}');
        $this->assertEquals('success', $view->getAssign('test'));
    }
}