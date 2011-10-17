<?php
/**
 * Selenium test case
 *
 * @link http://www.shopware.de
 * @copyright Copyright (c) 2011, shopware AG
 * @author Heiner Lohaus
 * @package Enlight
 * @subpackage Tests
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
     * @return void
     */
    public function verifyText($selector, $content)
    {
    	return $this->assertElementContainsText($selector, $content);
    }
    
    /**
     * Verify text present method
     *
     * @param string $content
     * @return void
     */
    public function verifyTextPresent($content)
    {
    	return $this->assertContains($content, $this->getBodyText());
    }
    
    /**
     * Returns screenshot url
     *
     * @return string
     */
    public function getFullScreenshotUrl()
    {
    	return $this->screenshotUrl.'/'.$this->testId.'.png';
    }
}