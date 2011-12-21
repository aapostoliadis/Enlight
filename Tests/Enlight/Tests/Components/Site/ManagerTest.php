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
 * @covers     Enlight_Components_Site
 */
class Enlight_Tests_Components_Site_ManagerTest extends Enlight_Components_Test_TestCase
{
    /**
     * @var Enlight_Components_Site_Manager
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $config = new Enlight_Config(array('sites' => array(
            array('id' => 1, 'name' => 'test'),
            array('id' => 2, 'name' => 'default', 'default' => true),
        )));

        $this->object = new Enlight_Components_Site_Manager($config);
    }

    /**
     * Test case
     */
    public function testFindOneBy()
    {
        $site = $this->object->findOneBy('name', 'test');
        $this->assertEquals('test', $site->getName());
    }

    /**
     * Test case
     */
    public function testGetDefault()
    {
        $site = $this->object->getDefault();
        $this->assertEquals(2, $site->getId());
    }
}