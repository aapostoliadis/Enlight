<?php
abstract class Enlight_Controller_Dispatcher_Dispatcher extends Enlight_Class
{
	protected $front;
	protected $response;
	
	public function setFront(Enlight_Controller_Front $controller)
    {
        $this->front = $controller;
        return $this;
    }
    
    public function Front()
    {
        return $this->front;
    }
    
    public function setResponse(Enlight_Controller_Response_Response $response = null)
    {
        $this->response = $response;
        return $this;
    }

    public function Response()
    {
        return $this->response;
    }
}