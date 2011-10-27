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
class Enlight_Tests_Config_AdapterDbTest extends Enlight_Components_Test_TestCase
{
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
            'dbname'   => $dir . 'config.db'
        ));

        $this->db->exec('
          CREATE TABLE IF NOT EXISTS `config_test` (
              `id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
              `section` varchar(255) DEFAULT NULL,
              `name` varchar(255) NOT NULL,
              `value` text NOT NULL,
              `created` datetime NOT NULL,
              `updated` datetime NOT NULL
            );
        ');
    }

    /**
     * Test case
     */
    public function testConfigFileWrite()
    {
        $adapter = new Enlight_Config_Adapter_DbTable(array(
            'automaticSerialization' => true,
            'namePrefix' => 'config_',
            'nameColumn' => 'name',
            'db' => $this->db
        ));
        
        $config = new Enlight_Config('test', array(
            'adapter' => $adapter,
            'section' => 'test',
            'allowModifications' => true
        ));
        $config->set('test', 1)->write();

        $config = new Enlight_Config('test', array(
            'adapter' => $adapter
        ));
        $this->assertEquals(1, $config->get('test'));
    }
}