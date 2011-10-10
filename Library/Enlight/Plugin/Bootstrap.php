<?php
/**
 * Enter description here...
 * 
 * @link http://www.shopware.de/
 * @author Heiner Lohaus
 * @package Enlight
 * @copyright Copyright (c) 2010, shopware AG
 * @version 1.0
 */
abstract class Enlight_Plugin_Bootstrap extends Enlight_Class implements Enlight_Singleton
{	
	protected $name;
	protected $namespace;
	
	public function __construct(Enlight_Plugin_Namespace $namespace, $name)
	{
		$this->namespace = $namespace;
		$this->name = $name;
		parent::__construct();
	}
	
	public function getName()
	{
		return $this->name;
	}
	
	public function getNamespace()
	{
		return $this->namespace;
	}
}