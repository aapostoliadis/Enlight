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
class Enlight_Tests_Config_DbTableTest extends Enlight_Components_Test_TestCase
{
    /**
     * @var Zend_Db_Adapter_Pdo_Sqlite
     */
    protected $db;
	/**
	 * @var Enlight_Components_Auth
	 */
	protected $auth;
	/**
	 * @var Enlight_Components_Auth_Adapter_DbTable
	 */
	protected $authAdapter;

	private $lockeduntilColumn;

    /**
     * Set up the test case
     */
    public function setUp()
    {
        parent::setUp();

        $dir = Enlight_TestHelper::Instance()->TestPath('TempFiles');
        $this->db = Enlight_Components_Db::factory('PDO_SQLITE', array(
            'dbname'   => $dir . 'auth.db'
        ));
		$this->lockeduntilColumn = 'lockeduntil';
		$this->createDb($this->lockeduntilColumn);
		$this->createDefaultUser($this->lockeduntilColumn);
		// Needed to simulate web environment - otherwise we would get a nasty notice.
		$GLOBALS['_SESSION'] = array();

		Zend_Session::$_unitTestEnabled = true;
		Zend_Session::start();

		$this->auth = Enlight_Components_Auth::getInstance();
		$this->authAdapter = new Enlight_Components_Auth_Adapter_DbTable($this->db,'s_core_auth','username','password');

    }

	/**
	 * Tearing down
	 *
	 * @return void
	 */
	public function tearDown()
	{
		$clearDb = "DROP TABLE IF EXISTS s_core_auth";
		$this->db->exec($clearDb);
	}

    /**
     * Test case successful login attempt
     */
    public function testDbLoginSuccess()
    {
		$this->authAdapter->setIdentity('demo')
							->setCredential(md5('demo'));

		$authResponse = $this->auth->authenticate($this->authAdapter);
		$this->assertTrue($authResponse->isValid());
    }

	/**
	 * Test case of an failed login attempt
	 */
	public function testDbLoginFailure()
	{
		$this->authAdapter->setIdentity('demo')
						->setCredential(md5('emod'));

		$authResponse = $this->auth->authenticate($this->authAdapter);
		$this->assertFalse($authResponse->isValid());
	}

	/**
	 * Test blocking system
	 *
	 * @return void
	 */
	public function testDbLoginSuspended()
	{
		$this->authAdapter->setIdentity('demo')
						->setCredential(md5('emod'));
		$this->authAdapter->setLockedUntilColumn($this->lockeduntilColumn);
		$this->authAdapter->setLockSeconds(30);
		$this->assertEquals($this->getAccountLockDate($this->lockeduntilColumn), '0000-00-00 00:00:00');
		
		$authResponse = $this->auth->authenticate($this->authAdapter);

		$this->assertNotEquals($this->getAccountLockDate($this->lockeduntilColumn), '0000-00-00 00:00:00');
		
		$this->assertFalse($authResponse->isValid());
		$this->auth = Enlight_Components_Auth::getInstance();

		$this->authAdapter = new Enlight_Components_Auth_Adapter_DbTable($this->db,'s_core_auth','username','password');
		$this->authAdapter->setIdentity('demo')
							->setCredential(md5('demo'));
		$this->authAdapter->setLockedUntilColumn($this->lockeduntilColumn);
		$authResponse = $this->auth->authenticate($this->authAdapter);
		$this->assertTrue($authResponse->isValid());

	}

	public function testSetLockedUntilColumn()
	{
		$clearDb = "DROP TABLE IF EXISTS s_core_auth";
		$this->db->exec($clearDb);

		$this->createDb('qwetz');
		$this->createDefaultUser('qwetz');
		$this->authAdapter->setIdentity('demo')
						->setCredential(md5('emod'));
		$this->authAdapter->setLockedUntilColumn('qwetz');
		$this->authAdapter->setLockSeconds(30);
		$this->assertEquals($this->getAccountLockDate('qwetz'), '0000-00-00 00:00:00');
	}

	private function getAccountLockDate($lockeduntilColumn)
	{
		$sql = "SELECT ".$lockeduntilColumn." FROM s_core_auth";
		return $this->db->fetchOne($sql);
	}

	private function createDb($lockeduntilColumn='lockeduntil')
	{
		 $this->db->exec('
          CREATE TABLE IF NOT EXISTS `s_core_auth` (
			  `id` int(11) NOT NULL,
			  `username` varchar(255) NOT NULL,
			  `password` varchar(60) NOT NULL,
			  `sessionID` varchar(50) NOT NULL,
			  `lastlogin` datetime NOT NULL DEFAULT \'0000-00-00 00:00:00\',
			  `name` varchar(255) NOT NULL,
			  `email` varchar(255) NOT NULL,
			  `active` int(1)  NOT NULL,
			  `rights` text NOT NULL,
			  `salted` int(1),
			  `failedlogins` int(11) NOT NULL,
			  `'.$lockeduntilColumn.'` datetime NOT NULL,
		  PRIMARY KEY (`id`)
            );
        ');
	}

	private function createDefaultUser($lockeduntilColumn)
	{
		$userAdd = "
				INSERT INTO `s_core_auth`
					(	`id`,
						`username`,
						`password`,
						`sessionID`,
						`lastlogin`,
						`name`,
						`email`,
						`active`,
						`rights`,
						`failedlogins`,
						`".$lockeduntilColumn."`)
					VALUES
					(	1,
						'demo',
						'fe01ce2a7fbac8fafaed7c982a04e229',
						's4inr04o6apmclk7u88qau4r57',
						'2011-09-28 17:28:24',
						'Administrator',
						'info@shopware.ag',
						 1,
						 '',
						 0,
						 '0000-00-00 00:00:00'
					);
		";
		$this->db->exec($userAdd);
	}

}