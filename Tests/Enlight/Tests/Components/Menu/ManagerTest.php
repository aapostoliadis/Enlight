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
class Enlight_Tests_Components_Menu_ManagerTest extends Enlight_Components_Test_TestCase
{
    /**
     * Test case
     */
    public function testAddItems()
    {
        $manager = new Enlight_Components_Menu(new Zend_Config(array(
            array('id' => 1, 'name' => 'parent'),
            array('id' => 2, 'name' => 'child', 'parent' => 1)
        )));

        $itemChild = $manager->findOneBy('id', 2);
        $itemParent = $manager->findOneBy('id', 1);

        $this->assertInstanceOf('Enlight_Components_Menu_Item', $itemChild);
        $this->assertInstanceOf('Enlight_Components_Menu_Item', $itemParent);

        $this->assertEquals($itemParent, $itemChild->getParent());
    }

    /**
     * Test case
     */
    public function testAddItemsInvert()
    {
        $manager = new Enlight_Components_Menu(new Zend_Config(array(
            array('id' => 2, 'name' => 'child', 'parent' => 1),
            array('id' => 1, 'name' => 'parent')
        )));

        $itemChild = $manager->findOneBy('id', 2);
        $itemParent = $manager->findOneBy('id', 1);

        $this->assertInstanceOf('Enlight_Components_Menu_Item', $itemChild);
        $this->assertInstanceOf('Enlight_Components_Menu_Item', $itemParent);

        $this->assertEquals($itemParent, $itemChild->getParent());
    }

    /**
     * Test case
     */
    public function testAddItem()
    {
        $manager = new Enlight_Components_Menu();

        $itemParent = new Zend_Config(array('id' => 1, 'name' => 'parent'));
        $this->assertInstanceOf('Enlight_Components_Menu', $manager->addItem($itemParent));

        $itemChild = new Zend_Config(array('id' => 2, 'name' => 'child', 'parent' => 1));
        $this->assertInstanceOf('Enlight_Components_Menu', $manager->addItem($itemChild));

        $itemChild = $manager->findOneBy('id', 2);
        $itemParent = $manager->findOneBy('id', 1);

        $this->assertInstanceOf('Enlight_Components_Menu_Item', $itemChild);
        $this->assertInstanceOf('Enlight_Components_Menu_Item', $itemParent);

        $this->assertEquals($itemParent, $itemChild->getParent());
    }
}