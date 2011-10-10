<?php
/**
 * Router object for use with Enlight_Controller 
 */
abstract class Enlight_Controller_Router_Router extends Enlight_Class
{
	protected $front;
	
	/**
	 * Set front controller
	 *
	 * @param Enlight_Controller_Front $front
	 * @return Enlight_Controller_Router_Router
	 */
	public function setFront(Enlight_Controller_Front $front)
    {
        $this->front = $front;
        return $this;
    }
    
    /**
     * Returns front controller
     *
     * @return Enlight_Controller_Front
     */
    public function Front()
    {
    	return $this->front;
    }
}