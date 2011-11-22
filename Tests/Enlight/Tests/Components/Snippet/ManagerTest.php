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
class Enlight_Tests_Components_Snippet_ManagerTest extends Enlight_Components_Test_TestCase
{
    /**
     * @var Enlight_Config_Adapter
     */
    protected $adapter;

    /**
     * Set up the test case
     */
    public function setUp()
    {
        parent::setUp();

        $dir = Enlight_TestHelper::Instance()->TestPath('TempFiles');
        $this->adapter = new Enlight_Config_Adapter_File(array(
            'configType' => 'ini',
            'configDir' => $dir
        ));
    }

    /**
     * Test case
     */
    public function testConstruct()
    {
        $manager = new Enlight_Components_Snippet_Manager($this->adapter);
        $this->assertEquals($this->adapter, $manager->Adapter());

        $manager = new Enlight_Components_Snippet_Manager(array(
            'adapter' => $this->adapter
        ));
        $this->assertEquals($this->adapter, $manager->Adapter());
    }

    /**
     * Test case
     */
    public function testGetNamespace()
    {
        $manager = new Enlight_Components_Snippet_Manager(array(
            'adapter' => $this->adapter
        ));
        $this->assertInstanceOf(
            'Enlight_Components_Snippet_Namespace',
            $manager->getNamespace('test')
        );
    }
}