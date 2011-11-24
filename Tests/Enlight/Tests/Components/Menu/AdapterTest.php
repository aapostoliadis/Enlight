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
class Enlight_Tests_Components_Menu_AdapterTest extends Enlight_Components_Test_TestCase
{
    /**
     * @var Enlight_Components_Menu_Adapter
     */
    protected $adapter;

    /**
     * @var Zend_Db_Adapter_Pdo_Sqlite
     */
    protected $db;

    /**
     * Set up the test case
     */
    public function setUp()
    {
        parent::setUp();

        $dir = Enlight_TestHelper::Instance()->TestPath('TempFiles');
        
        $this->db = Enlight_Components_Db::factory('PDO_SQLITE', array(
            'dbname'   => $dir . 'menu.db'
        ));
        $this->db->exec('
            DROP TABLE IF EXISTS `menu_test`;
            CREATE TABLE IF NOT EXISTS `menu_test` (
              `id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
              `parent` INTEGER NOT NULL,
              `active` INTEGER(1) NOT NULL,
              `label` varchar(255) NOT NULL
            );
            DROP TABLE IF EXISTS `menu_test2`;
            CREATE TABLE IF NOT EXISTS `menu_test2` (
              `id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
              `parent` INTEGER NOT NULL,
              `active` INTEGER(1) NOT NULL,
              `name` varchar(255) NOT NULL
            );
        ');
        
        $this->adapter = new Enlight_Components_Menu_Adapter_DbTable(array(
            'db' => $this->db,
            'name' => 'menu_test'
        ));
    }

    /**
     * Test case
     */
    public function testSetAdapter()
    {
        $manager = new Enlight_Components_Menu(new Zend_Config(array(
            array('id' => 1, 'label' => 'parent'),
            array('id' => 2, 'label' => 'child', 'parent' => 1)
        )));
        $this->assertEquals($manager, $manager->setAdapter($this->adapter));
        $this->assertEquals($this->adapter, $manager->getAdapter());
    }

    /**
     * Test case
     */
    public function testWriteSave()
    {
        $config = array(
            array('label' => 'child'),
            array('label' => 'child2'),
        );

        $expected = new Enlight_Components_Menu($config);
        $expected->setAdapter($this->adapter)->write();

        $manager = new Enlight_Components_Menu();
        $manager->setAdapter($this->adapter)->read();

        $this->assertArrayCount(2, $manager->toArray());
    }

    /**
     * Test case
     */
    public function testUpdate()
    {
        $config = array(
            array('label' => 'child'),
        );

        $expected = new Enlight_Components_Menu($config);
        $expected->setAdapter($this->adapter)->write();

        $expected->findOneBy('label', 'child')->setLabel('update');
        $expected->write();

        $manager = new Enlight_Components_Menu();
        $manager->setAdapter($this->adapter)->read();

        $this->assertNotEmpty($manager->findOneBy('label', 'update'));
    }

    /**
     * Test case
     */
    public function testSetColumn()
    {
        $this->adapter = new Enlight_Components_Menu_Adapter_DbTable(array(
            'db' => $this->db,
            'name' => 'menu_test2',
            'labelColumn' => 'name'
        ));

        $config = array(
            array('label' => 'child'),
        );

        $expected = new Enlight_Components_Menu($config);
        $expected->setAdapter($this->adapter)->write();

        $manager = new Enlight_Components_Menu();
        $manager->setAdapter($this->adapter)->read();

        $this->assertNotEmpty($manager->findOneBy('label', 'child'));
    }
}