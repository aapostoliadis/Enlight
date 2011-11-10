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

        $this->db->exec('
          CREATE TABLE IF NOT EXISTS `s_core_auth` (
			  `id` int(11) NOT NULL,
			  `username` varchar(255) NOT NULL,
			  `password` varchar(60) NOT NULL,
			  `sessionID` varchar(50) NOT NULL,
			  `lastlogin` datetime NOT NULL DEFAULT \'0000-00-00 00:00:00\',
			  `name` varchar(255) NOT NULL,
			  `email` varchar(120) NOT NULL,
			  `active` int(1) NOT NULL DEFAULT \'0\',
			  `sidebar` int(1) NOT NULL DEFAULT \'0\',
			  `window_height` int(11) NOT NULL,
			  `window_width` int(11) NOT NULL,
			  `window_size` text NOT NULL,
			  `admin` int(1) NOT NULL,
			  `rights` text NOT NULL,
			  `salted` int(1)  NOT NULL,
			  `failedlogins` int(11) NOT NULL,
			  `lockeduntil` datetime NOT NULL,
		  PRIMARY KEY (`id`)
            );
        ');
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
						`sidebar`,
						`window_height`,
						`window_width`,
						`window_size`,
						`admin`,
						`rights`,
						`salted`,
						`failedlogins`,
						`lockeduntil`)
					VALUES
					(	1, 'demo', 'fe01ce2a7fbac8fafaed7c982a04e229', 's4inr04o6apmclk7u88qau4r57',
						'2011-09-28 17:28:24', 'Administrator', 'info@shopware.ag', 1, 1, 0, 0,
						 'a:1:{s:6:\"plugin\";a:1:{i:1680;a:2:{s:6:\"height\";i:785;s:5:\"width\";i:1310;}}}',
						 1, '', 1, 0, '0000-00-00 00:00:00'
					);
		";
		// Needed to simulate web environment - otherwise we would get a nasty notice.
		$GLOBALS['_SESSION'] = array();

		Zend_Session::$_unitTestEnabled = true;
		Zend_Session::start();

		$this->db->exec($userAdd);

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

		;
		
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
		$this->authAdapter->setLockedUntilColumn('lockeduntil');
		$this->authAdapter->setLockSeconds(30);
		$this->assertEquals($this->getAccountLockDate(), '0000-00-00 00:00:00');
		
		$authResponse = $this->auth->authenticate($this->authAdapter);

		$this->assertNotEquals($this->getAccountLockDate(), '0000-00-00 00:00:00');
		
		$this->assertFalse($authResponse->isValid());
		$this->auth = Enlight_Components_Auth::getInstance();
		$this->authAdapter = new Enlight_Components_Auth_Adapter_DbTable($this->db,'s_core_auth','username','password');
		$this->authAdapter->setIdentity('demo')
							->setCredential(md5('demo'));
		$this->authAdapter->setLockedUntilColumn('lockeduntil');
		$authResponse = $this->auth->authenticate($this->authAdapter);
		$this->assertTrue($authResponse->isValid());

	}

	private function getAccountLockDate()
	{
		$sql = "SELECT lockeduntil FROM s_core_auth";
		return $this->db->fetchOne($sql);
	}


}