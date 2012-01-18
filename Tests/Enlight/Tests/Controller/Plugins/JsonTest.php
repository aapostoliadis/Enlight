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
 */
class Enlight_Tests_Controller_Plugins_Json_JsonTest extends Enlight_Components_Test_Plugin_TestCase
{
	/**
	 * @var Enlight_Controller_Plugins_Json_Bootstrap
	 */
	private $json = null;

	public function setUp()
    {
		$this->json = $this->getMock('Enlight_Controller_Plugins_Json_Bootstrap', null, array('Json'));
        $viewRenderer = $this->getMock('Enlight_Controller_Plugins_ViewRenderer_Bootstrap', null, array('ViewRenderer'));

        $namespace = new Enlight_Plugin_Namespace_Loader('Controller');
        $namespace->registerPlugin($this->json);
        $namespace->registerPlugin($viewRenderer);
	}

	public function tearDown()
	{
		$this->json = null;
	}

	public function testSetEncoding()
	{
		$this->json->setEncoding('ISO-8859-15');
		$this->assertEquals('ISO-8859-15', $this->json->getEncoding());
	}

	public function testSetRenderer()
	{
		$this->assertInstanceOf('Enlight_Controller_Plugins_Json_Bootstrap', $this->json->setRenderer(true));
		$this->assertTrue($this->json->getRenderer());
		$this->assertInstanceOf('Enlight_Controller_Plugins_Json_Bootstrap', $this->json->setRenderer(false));
		$this->assertFalse($this->json->getRenderer());
	}

	public function testSetPadding()
	{
		$this->assertInstanceOf('Enlight_Controller_Plugins_Json_Bootstrap', $this->json->setPadding(true));
		$this->assertTrue($this->json->getPadding());
		$this->assertInstanceOf('Enlight_Controller_Plugins_Json_Bootstrap', $this->json->setPadding(false));
		$this->assertFalse($this->json->getPadding());
	}

	public function testPluginCall()
	{
		$request = $this->Request()
                        ->setModuleName('frontend')
                        ->setDispatched(true);
        $response = $this->Response();

		$action = $this->getMock('Enlight_Controller_Action', null, array($request, $response) );

		$eventArgs = $this->createEventArgs()->setSubject($action);

		$this->json->onPostDispatch($eventArgs);
		$this->assertEquals(200, $this->Response()->getHttpResponseCode());
	}
	
	public function testJavascriptHeader()
	{
		$this->json->setPadding(true);
		$this->assertTrue($this->json->getPadding());
		
		$request = $this->Request()
                        ->setModuleName('frontend')
						->setParam('callback', 'foo')
                        ->setDispatched(true);
        $response = $this->Response();

		$action = $this->getMock('Enlight_Controller_Action',null,	array($request, $response) );
		$eventArgs = $this->createEventArgs()->setSubject($action)->setRequest($request)->setResponse($response);
		$this->json->onPostDispatch($eventArgs);
		$this->assertEquals(200, $this->Response()->getHttpResponseCode());

		$headers = $this->Response()->getHeaders();
		
		$this->assertArrayHasKey('value', $headers[0]);
		$this->assertEquals('text/javascript',$headers[0]['value']);
	}

	public function testWithPaddingWithOutCallback()
	{
		$this->json->setPadding(true);
		$this->assertTrue($this->json->getPadding());

		$request = $this->Request()
                        ->setModuleName('frontend')
                        ->setDispatched(true);
        $response = $this->Response();

		$action = $this->getMock('Enlight_Controller_Action',null,	array($request, $response) );
		$eventArgs = $this->createEventArgs()->setSubject($action)->setRequest($request)->setResponse($response);
		$this->json->onPostDispatch($eventArgs);
		$this->assertEquals(200, $this->Response()->getHttpResponseCode());
		
		$headers = $this->Response()->getHeaders();
		$this->assertArrayCount(0, $headers);
	}


	public function testWithoutPadding()
	{
        return;
		$this->json->setPadding(false);
		$this->json->setRenderer(true);

		$request = $this->Request()
                        ->setModuleName('frontend')
                        ->setDispatched(true);
        $response = $this->Response();

		$action = $this->getMock('Enlight_Controller_Action',null,	array($request, $response) );

        $action->View()->loadTemplate('string:');
        $action->View()->assign('foo','bar');
		$action->View()->assign('a', array(1,2,3));

		$eventArgs = $this->createEventArgs()->setSubject($action)->setRequest($request)->setResponse($response);
		$this->json->onPostDispatch($eventArgs);
		$this->assertEquals(200, $this->Response()->getHttpResponseCode());
		$headers = $this->Response()->getHeaders();
		$this->assertArrayHasKey('value', $headers[0]);
		$this->assertEquals('application/json',$headers[0]['value']);
	}

	public function testRendererOnPaddingOff()
	{
        return;
		$this->json->setRenderer(true);
		$this->json->setPadding(false);

		$request = $this->Request()
                        ->setModuleName('frontend')
                        ->setDispatched(true);
        $response = $this->Response();

		$action = $this->getMock('Enlight_Controller_Action',null,	array($request, $response) );

        $action->View()->loadTemplate('string:');
        $action->View()->assign('foo','bar');

		$eventArgs = $this->createEventArgs()->setSubject($action)->setRequest($request)->setResponse($response);
		$this->json->onPostDispatch($eventArgs);
		$this->assertEquals(200, $this->Response()->getHttpResponseCode());
		$headers = $this->Response()->getHeaders();
		$this->assertArrayHasKey('value', $headers[0]);
		$this->assertEquals('application/json',$headers[0]['value']);
		$this->assertContains('{"foo":"bar"', $this->Response()->getBody());
	}
	public function testRendererOnPaddingOn()
	{
        return;
		$this->json->setRenderer(true);
		$this->json->setPadding(true);

		$request = $this->Request()
                        ->setModuleName('frontend')
						->setParam('callback', 'foo')
                        ->setDispatched(true);
        $response = $this->Response();

		$action = $this->getMock('Enlight_Controller_Action',null,	array($request, $response) );

        $action->View()->loadTemplate('string:');
        $action->View()->assign('foo','bar');

		$eventArgs = $this->createEventArgs()->setSubject($action)->setRequest($request)->setResponse($response);
		$this->json->onPostDispatch($eventArgs);
		$this->assertEquals(200, $this->Response()->getHttpResponseCode());
		$headers = $this->Response()->getHeaders();
		$this->assertArrayHasKey('value', $headers[0]);
		$this->assertEquals('text/javascript', $headers[0]['value']);
		$this->assertContains('foo({"foo":"bar"', $this->Response()->getBody());
	}
	
	public function testRendererOffPaddingOff()
	{
        return;
		$this->json->setRenderer(false);
		$this->json->setPadding(false);

		$this->Response()->setBody('test Data');

		$request = $this->Request()
                        ->setModuleName('frontend')
						->setParam('callback', 'foo')
                        ->setDispatched(true);
        $response = $this->Response();

		$action = $this->getMock('Enlight_Controller_Action',null,	array($request, $response) );

        $action->View()->loadTemplate('string:');
        $action->View()->assign('foo','bar');

		$eventArgs = $this->createEventArgs()->setSubject($action)->setRequest($request)->setResponse($response);
		$this->json->onPostDispatch($eventArgs);
		$this->assertEquals('test Data', $this->Response()->getBody());
	}

	public function testRendererOffPaddingOn()
	{
        return;
		$this->json->setRenderer(false);
		$this->json->setPadding(true);

		$this->Response()->setBody('test Data');

		$request = $this->Request()
                        ->setModuleName('frontend')
						->setParam('callback', 'foo')
                        ->setDispatched(true);
        $response = $this->Response();

		$action = $this->getMock('Enlight_Controller_Action',null,	array($request, $response) );

        $action->View()->loadTemplate('string:');
        $action->View()->assign('foo','bar');

		$eventArgs = $this->createEventArgs()->setSubject($action)->setRequest($request)->setResponse($response);
		$this->json->onPostDispatch($eventArgs);
		$this->assertEquals('foo("test Data");', $this->Response()->getBody());
	}
}