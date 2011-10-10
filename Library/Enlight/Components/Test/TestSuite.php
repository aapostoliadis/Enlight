<?php
/**
 * Tests suite
 * 
 * @link http://www.shopware.de
 * @copyright Copyright (c) 2011, shopware AG
 * @author Heiner Lohaus
 * @package Enlight
 * @subpackage Tests
 */
class Enlight_Components_Test_TestSuite extends PHPUnit_Framework_TestSuite
{	
	/**
     * Adds a test to the suite.
     *
     * @param  PHPUnit_Framework_Test $test
     * @param  array $groups
     * @return Enlight_Components_Test_TestSuite
     */
	public function addTest(PHPUnit_Framework_Test $test, $groups = array())
    {
    	parent::addTest($test, $groups);
    	
    	return $this;
    }
    
    /**
     * Adds the tests from the given class to the suite.
     *
     * @param  mixed $testClass
     * @throws InvalidArgumentException
     * @return Enlight_Components_Test_TestSuite
     */
    public function addTestSuite($testClass)
    {
    	if (is_string($testClass) && class_exists($testClass)) {
            $testClass = new ReflectionClass($testClass);
        }
    	if($testClass instanceof ReflectionClass 
    	  && $testClass->isSubclassOf('PHPUnit_Framework_TestCase')) {
    		$this->addTest(new self($testClass));
    	} else {
    		parent::addTestSuite($testClass);
    	}
    	
    	return $this;
    }
    
    /**
     * Returns the test groups of the suite.
     *
     * @return array
     */
    public function getGroups()
    {
    	$groups = parent::getGroups();
    	if($this->getName()
    	  && !class_exists($this->getName(), false)) {
    		$groups[] = $this->getName();
    	}
    	return $groups;
    }
}