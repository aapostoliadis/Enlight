<?php
/**
 * Database tester
 * 
 * @link http://www.shopware.de
 * @copyright Copyright (c) 2011, shopware AG
 * @author Heiner Lohaus
 * @package Enlight
 * @subpackage Tests
 */
class Enlight_Components_Test_Database_DefaultTester extends PHPUnit_Extensions_Database_AbstractTester
{
	/**
     * @var PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    protected $connection;

    /**
     * Creates a new default database tester using the given connection.
     *
     * @param PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection
     */
    public function __construct(PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection=null)
    {
        $this->connection = $connection;
        
        parent::__construct();
    }

    /**
     * Returns the test database connection.
     *
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    public function getConnection()
    {
    	if($this->connection === null) {
    		$pdo = Enlight::Instance()->Db()->getConnection();
			$this->connection = $this->createDefaultDBConnection($pdo);
    	}
        return $this->connection;
    }    
    
    /**
     * Creates a new DefaultDatabaseConnection using the given PDO connection
     * and database schema name.
     *
     * @param PDO $connection
     * @param string $schema
     * @return PHPUnit_Extensions_Database_DB_DefaultDatabaseConnection
     */
    protected function createDefaultDBConnection(PDO $connection, $schema = '')
    {
        return new PHPUnit_Extensions_Database_DB_DefaultDatabaseConnection($connection, $schema);
    }
}