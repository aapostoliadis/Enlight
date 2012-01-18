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
 * @package    Enlight_Extensions
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 * @version    $Id$
 * @author     Heiner Lohaus
 * @author     $Author$
 */

/**
 * @category   Enlight
 * @package    Enlight_Extensions
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
class Enlight_Extensions_ErrorHandler_Bootstrap extends Enlight_Plugin_Bootstrap_Config
{
    /**
     * @var callback
     */
    protected $origErrorHandler = null;

    /**
     * @var boolean
     */
    protected $registeredErrorHandler = false;

    /**
     * @var array
     */
    protected $errorHandlerMap = null;

    /**
     * @var array
     */
    protected $errorLevel = 0;

    /**
     * @var array
     */
    protected $errorLog = false;

    /**
     * @var array
     */
    protected $errorList = array();

    /**
     * @var array
     */
    protected $errorLevelList = array(E_ERROR => 'E_ERROR', E_WARNING => 'E_WARNING', E_PARSE => 'E_PARSE', E_NOTICE => 'E_NOTICE', E_CORE_ERROR => 'E_CORE_ERROR', E_CORE_WARNING => 'E_CORE_WARNING', E_COMPILE_ERROR => 'E_COMPILE_ERROR', E_COMPILE_WARNING => 'E_COMPILE_WARNING', E_USER_ERROR => 'E_USER_ERROR', E_USER_WARNING => 'E_USER_WARNING', E_USER_NOTICE => 'E_USER_NOTICE', E_ALL => 'E_ALL', E_STRICT => 'E_STRICT', E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',//E_DEPRECATED        => 'E_DEPRECATED',
        //E_USER_DEPRECATED    => 'E_USER_DEPRECATED',
    );

    /**
     * @return void
     */
    public function init()
    {
        if (defined('E_DEPRECATED')) {
            $this->errorLevelList[E_DEPRECATED] = 'E_DEPRECATED';
        }
        if (defined('E_USER_DEPRECATED')) {
            $this->errorLevelList[E_USER_DEPRECATED] = 'E_USER_DEPRECATED';
        }
    }

    /**
     * Plugin install method
     */
    public function install()
    {
        $this->subscribeEvent('Enlight_Controller_Front_StartDispatch', 'onStartDispatch');
    }

    /**
     * Plugin event method
     */
    public function onStartDispatch()
    {
        $this->registerErrorHandler(E_ALL | E_STRICT);
    }

    /**
     * Register error handler callback
     *
     * @link    http://www.php.net/manual/en/function.set-error-handler.php Custom error handler
     * @param   int $errorLevel
     * @return  Enlight_Extensions_ErrorHandler_Bootstrap
     */
    public function registerErrorHandler($errorLevel = null)
    {
        if ($errorLevel === NULL) {
            $errorLevel = E_ALL | E_STRICT;
        }

        // Only register once.  Avoids loop issues if it gets registered twice.
        if ($this->registeredErrorHandler) {
            return $this;
        }

        $this->origErrorHandler = set_error_handler(array($this, 'errorHandler'), $errorLevel);

        $this->registeredErrorHandler = true;
        return $this;
    }

    /**
     * Error Handler will convert error into log message, and then call the original error handler
     *
     * @link http://www.php.net/manual/en/function.set-error-handler.php Custom error handler
     * @param   int    $errorLevel
     * @param   string $errorMessage
     * @param   string $errorFile
     * @param   int    $errorLine
     * @param   array  $errorContext
     * @return  bool
     */
    public function errorHandler($errorLevel, $errorMessage, $errorFile, $errorLine, $errorContext)
    {
        if ($this->errorLog) {
            $hashId = md5($errorLevel . $errorMessage . $errorFile . $errorLine);
            if (!isset($this->errorList[$hashId])) {
                $errorName = isset($this->errorLevelList[$errorLevel]) ? $this->errorLevelList[$errorLevel] : '';
                $this->errorList[$hashId] = array(
                    'count' => 1,
                    'code' => $errorLevel,
                    'name' => $errorName,
                    'message' => $errorMessage,
                    'line' => $errorLine,
                    'file' => $errorFile
                );
            } else {
                ++$this->errorList[$hashId]['count'];
            }
        }

        //throw new ErrorException($errorMessage, 0, $errorLevel, $errorFile, $errorLine);

        if ($this->origErrorHandler !== null) {
            return call_user_func(
                $this->origErrorHandler,
                $errorLevel,
                $errorMessage,
                $errorFile,
                $errorLine,
                $errorContext
            );
        }
        return true;
    }

    /**
     * Returns error log list
     *
     * @return  array
     */
    public function getErrorLog()
    {
        return $this->errorList;
    }

    /**
     * Sets enabled log flag
     *
     * @param   bool $value
     * @return  Shopware_Plugins_Core_ErrorHandler_Bootstrap
     */
    public function setEnabledLog($value = true)
    {
        $this->errorLog = $value ? true : false;
        return $this;
    }
}