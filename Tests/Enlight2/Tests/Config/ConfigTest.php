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
class Enlight_Tests_Config_ConfigTest extends Enlight_Components_Test_TestCase
{
    /**
     * Test case
     */
	public function testConfigConstructor()
    {
		$this->setExpectedException('Enlight_Config_Exception');
    	$config = new Enlight_Config('');
    }

	/**
	 * Test case
	 */
	public function testConfigGet()
    {
    	$config = new Enlight_Config(array('test' => true));

    	$this->assertTrue($config->get('test'));
		$this->assertTrue($config->test);
    }

	/**
	 * Test case
	 */
	public function testConfigGetDefault()
    {
    	$config = new Enlight_Config(array());

    	$this->assertTrue($config->get('test', true));
    }

	/**
	 * Test case
	 */
	public function testConfigSet()
    {
    	$config = new Enlight_Config(array(), true);

		$config->set('test', true);
    	$this->assertTrue($config->get('test'));

		$config->test = false;
    	$this->assertFalse($config->test);
    }

	/**
	 * Test case
	 */
	public function testConfigSetArray()
    {
		$config = new Enlight_Config(array(), true);

		$config->set('test', array('test' => true));
    	$this->assertTrue($config->test->test);
	}

	/**
	 * Test case
	 */
	public function testConfigSetAllowModifications()
    {
    	$config = new Enlight_Config(array('test' => array('test' => true)));

		$config->setAllowModifications();
		$config->test->test = false;
		$this->assertFalse($config->test->test);
    }

	/**
	 * Test case
	 */
	public function testConfigArrayAccess()
    {
    	$config = new Enlight_Config(array(), true);
		$config['test'] = true;

		$this->assertTrue($config['test']);
		$this->assertTrue(isset($config['test']));

		unset($config['test']);

		$this->assertNull($config['test']);
    }

	/**
	 * Test case
	 */
	public function testConfigDirtyFields()
    {
		$config = new Enlight_Config(array(), true);
		$config->set('test', true);

		$this->assertEquals(array('test'), $config->getDirtyFields());

		$config->resetDirtyFields();

		$this->assertArrayCount(0, $config->getDirtyFields());

		$config->setDirtyFields(array('test'));

		$this->assertEquals(array('test'), $config->getDirtyFields());
	}

	/**
	 * Test case
	 */
	public function testSetSection()
    {
		$config = new Enlight_Config(array());

		$config->setSection('test');
		$this->assertEquals('test', $config->getSection());

		$config->setSection(array('test', 'test'));
		$this->assertEquals('test:test', $config->getSection());
	}

    /**
	 * Test case
	 */
	public function testSetExtendsArray()
    {
        $config = new Enlight_Config(array());

        $testExtends = array('test' => 'default',  'default' => array('test', 'test'));
        $config->setExtends($testExtends);

        $testExtends = array('test' => 'default',  'default' => 'test:test');
        $this->assertEquals($testExtends, $config->getExtends());
    }

    /**
	 * Test case
	 */
	public function testSetExtendsString()
    {
        $config = new Enlight_Config(array());

        $config->setSection('test');
        $config->setExtends('default');

        $testExtends = array('test' => 'default');
        $this->assertEquals($testExtends, $config->getExtends());
    }

    /**
	 * Test case
	 */
	public function testReadOnly()
    {
        $this->setExpectedException('Enlight_Config_Exception');

        $config = new Enlight_Config(array());
        $config->test = true;
    }

    public function testAutoNumArray()
    {
        $config = new Enlight_Config(array(), true);
        $config[0] = 'test1';
        $config[] = 'test2';

        $this->assertEquals('test1', $config->get(0));
        $this->assertEquals('test2', $config->get(1));
    }
}
