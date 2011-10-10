<?php
/**
 * Array tester
 * 
 * @link http://www.shopware.de
 * @copyright Copyright (c) 2011, shopware AG
 * @author Heiner Lohaus
 * @package Enlight
 * @subpackage Tests
 */
class Enlight_Components_Test_Constraint_ArrayCount extends PHPUnit_Framework_Constraint
{
	/**
     * @var int
     */
    protected $count;

    /**
     * Constructor method
     * 
     * @param int $count
     */
    public function __construct($count)
    {
        $this->count = $count;
    }
	
    /**
     * Evaluates the constraint for parameter $other. Returns TRUE if the
     * constraint is met, FALSE otherwise.
     *
     * @param mixed $other Value or object to evaluate.
     * @return bool
     */
    public function evaluate($other)
    {
    	return count($other)===$this->count;
    }
    
    /**
     * @param mixed   $other
     * @param string  $description
     * @param boolean $not
     */
    protected function customFailureDescription($other, $description, $not)
    {
        return sprintf(
          'Failed asserting that an array %s.',

           $this->toString()
        );
    }

    /**
     * Returns a string representation of the constraint.
     *
     * @return string
     */
    public function toString()
    {
        return  'has ' . PHPUnit_Util_Type::toString($this->count) .' values';
    }
}