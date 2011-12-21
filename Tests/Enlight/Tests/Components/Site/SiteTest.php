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
 * @covers     Enlight_Components_Site
 */
class Enlight_Tests_Components_Site_SiteTest extends Enlight_Components_Test_TestCase
{
    /**
     * Test case
     */
    public function testConstructOptions()
    {
        $site = new Enlight_Components_Site(new Enlight_Config(array(
            'id' => 1,
            'name' => 'test',
            'locale' => 'de_DE',
            'currency' => 'EUR',
            'host' => 'localhost',
            'template' => 'default',
            'test' => '1',
            'siteSwitch' => '2|3'
        )));

        $this->assertEquals(1, $site->getId());
        $this->assertEquals('test', $site->getName());
        $this->assertEquals('de_DE', $site->get('locale'));
        $this->assertEquals('EUR', $site->get('currency'));
        $this->assertEquals('localhost', $site->getHost());
        $this->assertEquals('default', $site->get('template'));
        $this->assertEquals('1', $site->get('test'));
        $this->assertEquals(array('2', '3'), $site->get('siteSwitch'));
    }

    /**
     * Test case
     */
    public function testSetOptions()
    {
        $site = new Enlight_Components_Site();

        $site->setOptions(array(
            'id' => 1,
            'name' => 'test',
            'locale' => 'de_DE',
            'currency' => 'EUR',
            'host' => 'localhost',
            'template' => 'default',
            'test' => '1',
            'siteSwitch' => '2|3'
        ));

        $this->assertEquals(1, $site->getId());
        $this->assertEquals('test', $site->getName());
        $this->assertEquals('de_DE', $site->get('locale'));
        $this->assertEquals('EUR', $site->get('currency'));
        $this->assertEquals('localhost', $site->getHost());
        $this->assertEquals('default', $site->get('template'));
        $this->assertEquals('1', $site->get('test'));
        $this->assertEquals(array('2', '3'), $site->get('siteSwitch'));
    }

    /**
     * Test case
     */
    public function testSetter()
    {
        $site = new Enlight_Components_Site();

        $site->set('test', 1);
        $this->assertEquals(1, $site->get('test'));

        $site->set('locale', 'en_GB');
        $this->assertEquals('en_GB', $site->get('locale'));
    }

    /**
     * Test case
     */
    public function testGetUndefined()
    {
        $site = new Enlight_Components_Site();
        $this->assertNull($site->get('test'));
    }

    /**
     * Test case
     */
    public function testSetHost()
    {
        $site = new Enlight_Components_Site();
        $_SERVER['HTTP_HOST'] = 'test';
        $site->setHost();
        $this->assertEquals('test', $site->get('host'));
        $_SERVER['HTTP_HOST'] = 'test2';
        $site->setHost();
        $this->assertEquals('test', $site->get('host'));
    }

    /**
     * Test case
     */
    public function testSetLocale()
    {

    }

    /**
     * Test case
     */
    public function testSetCurrency()
    {

    }

    /**
     * Test case
     */
    public function testSetTemplate()
    {

    }

    /**
     * Test case
     */
    public function testGetId()
    {

    }

    /**
     * Test case
     */
    public function testGetName()
    {
        $site = new Enlight_Components_Site();
        $site->set('id', 1);
        $this->assertEquals('site1', $site->getName());
    }

    /**
     * Test case
     */
    public function testGetter()
    {
        $site = new Enlight_Components_Site();

        $_SERVER['HTTP_HOST'] = 'test2';
        Enlight_Components_Locale::setDefault('de_DE');

        $this->assertInstanceOf('Zend_Locale', $site->Locale());
        $this->assertInstanceOf('Zend_Currency', $site->Currency());
        $this->assertInstanceOf('Enlight_Template_Manager', $site->Template());
        $this->assertInternalType('string', $site->getHost());
    }

    /**
     * Test case
     */
    public function testGetResources()
    {
        $site = new Enlight_Components_Site();
        $site->setLocale('de_DE');

        $this->assertCount(1, $site->getResources());
    }

    /**
     * Test case
     */
    public function testSleep()
    {
        $site = new Enlight_Components_Site();
        $site->set('name', 'test');

        $site = serialize($site);
        $site = unserialize($site);

        $this->assertEquals('test', $site->getName());
    }
}
