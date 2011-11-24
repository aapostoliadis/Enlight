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
class Enlight_Tests_Template_PluginTest extends Enlight_Components_Test_TestCase
{
    /**
     * @var Enlight_Template_Manager
     */
    protected $manager;

    /**
     * Set up the test case
     */
    public function setUp()
    {
        parent::setUp();

        $tempDir = Enlight_TestHelper::Instance()->TestPath('TempFiles');
        $dataDir = Enlight_TestHelper::Instance()->TestPath('DataSets');

        $this->manager = new Enlight_Template_Manager();
        
        $this->manager->setCompileDir($tempDir);
        $this->manager->setCompileId('resource');
        $this->manager->setTemplateDir(array(
            $dataDir . 'Template/parent1/',
            $dataDir . 'Template/parent2/'
        ));
    }

    /**
     * Test case
     */
    public function testParent()
    {
        $this->assertEquals('test success', $this->manager->fetch('index.tpl'));
    }

    /**
     * Test case
     */
    public function testParentFail()
    {
        $this->setExpectedException('SmartyCompilerException');
        $this->manager->fetch('fail.tpl');
    }

    /**
     * Test case
     */
    public function testTemplateExists()
    {
        $result = $this->manager->templateExists('index.tpl');
        $this->assertEquals('success', $result);

        $result = $this->manager->fetch('string:{if {"index.tpl"|template_exists}}true{/if}');
        $this->assertEquals('true', $result);

        $result = $this->manager->fetch('string:{if {"test.tpl"|template_exists}}true{else}false{/if}');
        $this->assertEquals('false', $result);
    }
}