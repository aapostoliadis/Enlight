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
 * @package    Enlight_Exception
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 * @version    $Id$
 * @author     Heiner Lohaus
 * @author     $Author$
 */

/**
 * The Enlight_Exception is the basic class for each specified exception class. (Controller_Exception, ...)
 * Extends the standard exception class with an previous Exception property
 *
 * @category   Enlight
 * @package    Enlight_Exception
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
class Enlight_Exception extends Exception
{
    const CLASS_NOT_FOUND = 1000;
    const METHOD_NOT_FOUND = 1100;
    const PROPERTY_NOT_FOUND = 1200;

    /**
     * @var Exception|null
     */
    protected $previous;

    /**
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct($message = '', $code = 0, Exception $previous = null)
    {
        if ($previous !== null) {
            $this->previous = $previous;
        }

        parent::__construct($message, $code);

        if (in_array($code, array(self::CLASS_NOT_FOUND, self::METHOD_NOT_FOUND, self::PROPERTY_NOT_FOUND))) {
            $trace = debug_backtrace(false);
            foreach ($trace as $i => $var) {
                if (!$i || $var['function'] == '__call' || !isset($var['line'])) {
                    unset($trace[$i]);
                    continue;
                }
                $this->file = $var['file'];
                $this->line = $var['line'];
                break;
            }
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if (version_compare(PHP_VERSION, '5.3.0', '<')) {
            if ($this->previous !== null) {
                return $this->previous->__toString() . "\n\nNext " . parent::__toString();
            }
        }
        return parent::__toString();
    }
}