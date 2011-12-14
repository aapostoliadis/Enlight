<?php
/**
 * Enlight
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://enlight.de/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@shopware.de so we can send you a copy immediately.
 *
 * @category   Enlight
 * @package    Enlight_Test
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 * @version    $Id$
 * @author     Heiner Lohaus
 * @author     $Author$
 */

/**
 * @category   Enlight
 * @package    Enlight_Test
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
abstract class Enlight_Components_Test_Selenium_TestCase extends PHPUnit_Extensions_SeleniumTestCase
{
    protected $browserUrl = 'http://192.168.178.50/';
    protected $captureScreenshotOnFailure = true;

	/**
	 * Setup Shop - Set base url
	 * @return void
	 */
    protected function setUp()
    {
    	if($this->browserUrl !== null) {
    		$this->setBrowserUrl($this->browserUrl);
    	}
    	parent::setUp();
    }
    
    /**
     * Verify text method
     *
     * @param string $selector
     * @param string $content
     */
    public function verifyText($selector, $content)
    {
    	$this->assertElementContainsText($selector, $content);
    }
    
    /**
     * Verify text present method
     *
     * @param string $content
     */
    public function verifyTextPresent($content)
    {
    	$this->assertContains($content, $this->getBodyText());
    }
    
    /**
     * Returns the screen shot url
     *
     * @return string
     */
    public function getFullScreenshotUrl()
    {
    	return $this->screenshotUrl . '/'. $this->testId . '.png';
    }
}