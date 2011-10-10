<?php
class Enlight_Exception extends Exception
{
	const Class_Not_Found = 1000;
	const Method_Not_Found = 1100;
	const Property_Not_Found = 1200;
		
	protected $previous; 
	public function __construct($message = '', $code = 0, Exception $previous=null)
	{
		if(isset($previous))
		{
			$this->previous = $previous;
		}
		parent::__construct($message, $code);
		if(in_array($code, array(self::Class_Not_Found, self::Method_Not_Found, self::Property_Not_Found)))
		{
			$trace = debug_backtrace(false);
			foreach ($trace as $i=>$var)
			{
				if(!$i || $var['function']=='__call' || !isset($var['line']))
				{
					unset($trace[$i]);
					continue;
				}
				$this->file = $var['file'];
				$this->line = $var['line'];
				break;
			}
		}
	}
	public function __toString()
    {
        if (version_compare(PHP_VERSION, '5.3.0', '<')) {
            if ($this->previous!==null) {
                return $this->previous->__toString(). "\n\nNext ". parent::__toString();
            }
        }
        return parent::__toString();
    }
}