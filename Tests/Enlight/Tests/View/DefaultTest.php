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
 * @author     Oliver Denter
 * @author     $Author$
 */

/**
 * Test case
 *
 * @category   Enlight
 * @package    Enlight_Tests
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license/new-bsd     New BSD License
 * @covers     Enlight_View_Default
 */
class Enlight_Tests_View_DefaultTest extends Enlight_Components_Test_TestCase
{
    /**
     * @var Enlight_View_Default
     */
    protected $view;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     * @return void
     */
    public function setUp()
    {
        $manager = $this->getMock('Enlight_Template_Manager', null, array());
        $this->view = $this->getMock('Enlight_View_Default', null, array($manager));

        parent::setUp();
    }

    public function testEngine()
    {
        $this->assertInstanceOf('Enlight_Template_Manager', $this->view->Engine());
    }

    public function testSetTemplateDir()
    {
        $this->view->setTemplateDir("/var/www/shopware400/templates/_default");
        $this->assertEquals("/var/www/shopware400/templates/_default/", $this->view->Engine()->getTemplateDir(0));
    }

    public function testAddTemplateDir()
    {
        $this->view->addTemplateDir("/var/www/shopware400/templates/_default");
        $this->assertEquals("/var/www/shopware400/templates/_default/", $this->view->Engine()->getTemplateDir(1));
    }

    public function testSetTemplate()
    {
        $template = $this->getMock('Enlight_Template_Default', null, array(), '', false);
        $this->view->setTemplate($template);
        $this->assertTrue($this->view->hasTemplate());
    }

    public function testCreateTemplate()
    {
        $template = $this->getMock('Enlight_Template_Default', null, array(), '', false);
        $this->view->setTemplate($template);
        $tpl = $this->view->createTemplate("testTemplate");
        $this->assertInstanceOf('Enlight_Template_Default', $tpl);
        $this->assertEquals("testTemplate", $tpl->template_resource);
    }

    public function testTemplate()
    {
        $template = $this->getMock('Enlight_Template_Default', null, array(), '', false);
        $this->view->setTemplate($template);
        $this->assertInstanceOf('Enlight_Template_Default', $this->view->Template());
    }

    public function testLoadTemplate()
    {
        $this->view->loadTemplate('testTemplate');
        $this->assertTrue($this->view->hasTemplate());
    }

    public function testReplaceBlock()
    {
        $this->view->loadTemplate('string:{block name="testBlock"}-fest-{/block}');
        $this->view->extendsBlock('testBlock', 'erweiterung', 'replace');
        $this->assertEquals('erweiterung', $this->view->Template()->fetch());
    }

    public function testAppendBlock()
    {
        $this->view->loadTemplate('string:{block name="appendBlock"}-fest-{/block}');
        $this->view->extendsBlock('appendBlock', 'append', 'append');
        $this->assertEquals('-fest-append', $this->view->Template()->fetch());
    }

    public function testPrependBlock()
    {
        $this->view->loadTemplate('string:{block name="prependBlock"}-fest-{/block}');
        $this->view->extendsBlock('prependBlock', 'prepend', 'prepend');
        $this->assertEquals('prepend-fest-', $this->view->Template()->fetch());
    }

    public function testExtendsTemplate()
    {
        $this->view->loadTemplate('string:{block name="prependBlock"}-fest-{/block}');
        $this->view->extendsTemplate('string:{block name="prependBlock"}-fest2-{/block}');
        $this->assertEquals('-fest-|string:-fest2-', $this->view->Template()->fetch());
    }

    public function testTemplateExists()
    {
        $this->view->loadTemplate('string:{block name="testBlock"}festerWert{/block}');
        $this->assertTrue($this->view->templateExists('string:{block name="testBlock"}festerWert{/block}'));
    }

    public function testAssign()
    {
        $this->view->loadTemplate('string:{block name="testBlock"}{$variable}{/block}');
        $this->view->assign('variable', 'wert der variable', true, 1);
        $this->assertEquals('wert der variable', $this->view->Template()->fetch());
    }

    public function testClearAssign()
    {
        $this->view->loadTemplate('string:{block name="testBlock"}{$variable}{/block}');
        $this->view->assign('variable', 'wert der variable', true, 1);
        $this->view->clearAssign('variable', 1);
        $this->assertNull($this->view->getAssign('variable'));
    }

    public function testGetAssign()
    {
        $this->view->loadTemplate('string:{block name="testBlock"}{$variable}{/block}');
        $this->view->assign('variable', 'wert der variable', true, 1);
        $this->assertEquals('wert der variable', $this->view->getAssign('variable'));
    }

    public function testRender()
    {
        $this->view->loadTemplate('string:{block name="testBlock"}Content{/block}');
        $this->view->render();
        $this->assertEquals('Content', $this->view->Template()->fetch());
    }

    public function testSetNocache()
    {
        $this->view->loadTemplate('string:{block name="testBlock"}Content{$test}{/block}');

        $this->view->setNocache(true);
        $this->view->assign('test', 'Variable', null, 1);
        $this->assertTrue($this->view->Engine()->tpl_vars['test']->nocache);
        $this->view->clearAssign('test', 1);

        $this->view->assign('test', 'Variable', false, 1);
        $this->assertFalse($this->view->Engine()->tpl_vars['test']->nocache);
    }

    public function testSetScope()
    {
        $this->view->loadTemplate('string:{block name="testBlock"}Content{$test}{/block}');

        $this->view->setScope(1);
        $this->view->assign('test', 'Variable');
        $this->assertArrayCount(1, $this->view->Engine()->tpl_vars);

        $this->view->clearAssign();

        $this->view->setScope(3);
        $this->view->assign('test', 'Variable');
        $this->assertArrayCount(0, $this->view->Engine()->tpl_vars);
    }

    public function testSetCaching()
    {
        $this->view->loadTemplate('string:{block name="testBlock"}Content{$test}{/block}');
        $this->view->setCaching(true);
        $this->assertTrue($this->view->Template()->caching);

        $this->view->setCaching(false);
        $this->assertFalse($this->view->Template()->caching);
    }

    public function testIsCached()
    {
        $this->view->loadTemplate('string:{block name="testBlock"}Content{$test}{/block}');
        $this->view->setCaching(false);
        $this->view->render();
        $this->assertFalse($this->view->Template()->isCached());

        $this->view->loadTemplate('string:{block name="testBlock"}Content{$test}{/block}');
        $this->view->setCaching(true);
        $this->view->render();
        $this->assertTrue($this->view->Template()->isCached());
    }

    public function testSetCacheId()
    {
        $this->view->loadTemplate('string:{block name="testBlock"}Content{$test}{/block}');
        $this->view->setCacheId("123");
        $this->assertEquals("123", $this->view->getCacheId());
    }

    public function testGetCacheId()
    {
        $this->view->loadTemplate('string:{block name="testBlock"}Content{$test}{/block}');
        $this->view->setCacheId("123");
        $this->view->addCacheId("456");
        $this->assertArrayCount(2, explode('|', $this->view->getCacheId()));
    }
}
