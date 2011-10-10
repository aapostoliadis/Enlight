<?php
/**
 * Database test case
 * 
 * @link http://www.shopware.de
 * @copyright Copyright (c) 2011, shopware AG
 * @author Heiner Lohaus
 * @package Enlight
 * @subpackage Tests
 */
abstract class Enlight_Components_Test_Database_TestCase extends PHPUnit_Extensions_Database_TestCase
{
	/**
     * Returns the test database connection.
     *
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
	protected function getConnection()
	{
		$pdo = Enlight::Instance()->Db()->getConnection();
		return $this->createDefaultDBConnection($pdo);
    }
}