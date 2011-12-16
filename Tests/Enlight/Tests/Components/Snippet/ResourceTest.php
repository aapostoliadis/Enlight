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
class Enlight_Tests_Components_Snippet_ResourceTest extends Enlight_Components_Test_TestCase
{
    /**
     * @var Enlight_Components_Snippet_Manager
     */
    protected $manager;

    /**
     * @var Enlight_Template_Manager
     */
    protected $engine;

    /**
     * Set up the test case
     */
    public function setUp()
    {
        parent::setUp();

        $tempDir = Enlight_TestHelper::Instance()->TestPath('TempFiles');
        $adapter = new Enlight_Config_Adapter_File(array(
                    'configType' => 'ini',
                    'configDir' => $tempDir,
                    'namePrefix' => 'snippet_'
                ));
        $this->manager = new Enlight_Components_Snippet_Manager($adapter);

        $this->engine = new Enlight_Template_Manager();
        $this->engine->setCompileDir($tempDir);
        $this->engine->setCompileId('snippet');
        $this->engine->clearCompiledTemplate(null, 'snippet');
    }

    /**
     * Test case
     */
    public function testConstruct()
    {
        $resource = new Enlight_Components_Snippet_Resource($this->manager);
        $this->assertInstanceOf('Smarty_Internal_Resource_Extends', $resource);
    }

    /**
     * Test case
     */
    public function testPopulatePlugin()
    {
        /*
        $engine = $this->getMockBuilder('Enlight_Template_Manager')
                       ->disableOriginalConstructor()
                       ->getMock();

        $source = $this->getMockBuilder('Smarty_Template_Source')
                       ->disableOriginalConstructor()
                       ->getMock();

        $source->smarty = $engine;
        
        $source->expects($this->any())
               ->method('__get')
               ->will($this->returnValue($engine));

        $resource = new Enlight_Components_Snippet_Resource($this->manager);
        $resource->populate($source);
        */
    }

    /**
     * Test case
     */
    public function testCompileSnippetBlock()
    {
        $resource = new Enlight_Components_Snippet_Resource($this->manager);

        $this->assertEquals('', $resource->compileSnippetBlock(array(), null));
        $this->assertEquals('test', $resource->compileSnippetBlock(array(), 'test'));
        $this->assertEquals('#test#', $resource->compileSnippetBlock(array('name' => 'test'), ''));
        $this->assertContains('<span', $resource->compileSnippetBlock(array('tag' => 'span'), 'test'));
        $this->assertEmpty($resource->compileSnippetBlock(array('assign' => 'test'), 'test', $this->engine));
        $this->assertContains('class="test_test"', $resource->compileSnippetBlock(array('tag' => 'span', 'namespace' => 'test/test'), 'test'));
        $this->assertContains('class="test test_test"', $resource->compileSnippetBlock(array('tag' => 'span', 'namespace' => 'test/test', 'class' => 'test'), ''));
    }

    /**
     * Test case
     */
    public function testGetContent()
    {
        $resource = new Enlight_Components_Snippet_Resource($this->manager);

        $this->engine->registerResource('snippet', $resource);

        $this->assertEquals('test', $this->engine->fetch('snippet:string:{s name="test" namespace="test"}test{/s}'));
        $this->assertEquals('test', $this->engine->fetch('snippet:string:{namespace name="test"}{s name="test"}test{/s}'));

        $this->assertEquals('force', $this->engine->fetch('snippet:string:{s name="force" namespace="test" force}force{/s}'));
        $this->assertEquals('force2', $this->engine->fetch('snippet:string:{s name="force" namespace="test" force}force2{/s}'));

        $this->assertContains('<span', $this->engine->fetch('snippet:string:{se name="force" namespace="test"}force{/se}'));

        $this->assertEquals('test', $this->engine->fetch('snippet:string:{namespace name="ignore"}{s name="ignore"}test{/s}'));
        $this->assertEquals('ignore', $this->engine->fetch('snippet:string:{namespace ignore}{s name="ignore"}ignore{/s}'));
    }

    /**
     * Test case
     */
    public function testGetSnippetContent()
    {

    }

    /**
     * Test case
     */
    public function testGetSnippetNamespace()
    {

    }
}