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
class Enlight_Extensions_Debug_Bootstrap extends Enlight_Plugin_Bootstrap_Config
{
    /**
     * @var Enlight_Components_Log
     */
    protected $log;

    /**
     * Plugin install method
     */
    public function install()
    {
        $this->subscribeEvent('Enlight_Controller_Front_StartDispatch', null, 'onStartDispatch');

        $this->subscribeEvent('Enlight_Controller_Front_DispatchLoopShutdown', null, 'onDispatchLoopShutdown');
    }

    /**
     * Plugin event method
     *
     * @param   Enlight_Event_EventArgs $args
     */
    public function onStartDispatch(Enlight_Event_EventArgs $args)
    {
        $this->log = $this->Application()->Log();

        /*
        $request = $args->getSubject()->Request();

        if ($request->getClientIp(false)
          && !empty($config->allowIp)
          && strpos($config->allowIp, $request->getClientIp(false))===false){
            return;
        }
        */

        /** @var $errorHandler  */
        $errorHandler = $this->Application()->Extensions()->ErrorHandler();
        $errorHandler->setEnabledLog(true);
        $errorHandler->registerErrorHandler(E_ALL | E_STRICT);
    }

    /**
     * Plugin event method
     *
     * @param   Enlight_Event_EventArgs $args
     */
    public function onAfterRenderView(Enlight_Event_EventArgs $args)
    {
        $template = $args->getTemplate();
        $this->logTemplate($template);
    }

    /**
     * Plugin event method
     *
     * @param   Enlight_Event_EventArgs $args
     */
    public function onDispatchLoopShutdown(Enlight_Event_EventArgs $args)
    {
        $errorHandler = $this->Application()->Extensions()->ErrorHandler();
        $this->logError($errorHandler);

        $response = $this->Application()->Front()->Response();
        $this->logException($response);

        $template = $this->Application()->Template();
        $this->logTemplate($template);
    }

    /**
     * Log error method
     *
     * @param   $errorHandler
     */
    public function logError($errorHandler)
    {
        $errors = $errorHandler->getErrorLog();
        if (empty($errors)) {
            return;
        }

        $counts = array();
        foreach ($errors as $errorKey => $error) {
            $counts[$errorKey] = $error['count'];
        }
        array_multisort($counts, SORT_NUMERIC, SORT_DESC, $errors);

        $rows = array();
        foreach ($errors as $error) {
            if (!$rows) {
                $rows[] = array_keys($error);
            }
            $rows[] = array_values($error);
        }
        $table = array('Error Log (' . count($errors) . ')', $rows);

        $this->log->table($table);
    }

    /**
     * Log template method
     *
     * @param   $template
     */
    public function logTemplate($template)
    {
        $template_vars = $template->getTemplateVars();
        unset($template_vars['smarty']);
        if (empty($template_vars)) {
            return;
        }
        $rows = array(array('spec', 'value'));
        foreach ($template_vars as $template_spec => $template_var) {
            $template_var = $this->encode($template_var);
            $rows[] = array($template_spec, $template_var);
        }
        $table = array('Template Vars (' . (count($template_vars)) . ')', $rows);

        $this->log->table($table);

        $config_vars = $template->getConfigVars();
        if (!empty($config_vars)) {
            $rows = array(array('spec', 'value'));
            foreach ($config_vars as $config_spec => $config_var) {
                $rows[] = array($config_spec, $config_var);
            }
            $table = array('Config Vars', $rows);
            $this->log->table($table);
        }
    }

    /**
     * Log exception method
     *
     * @param   $response
     */
    public function logException($response)
    {
        $exceptions = $response->getException();
        if (empty($exceptions)) {
            return;
        }

        $rows = array(array('code', 'name', 'message', 'line', 'file', 'trace'));
        foreach ($exceptions as $exception) {
            $rows[] = array(
                $exception->getCode(),
                get_class($exception),
                $exception->getMessage(),
                $exception->getLine(),
                $exception->getFile(),
                explode("\n", $exception->getTraceAsString())
            );
        }
        $table = array('Exception Log (' . count($exceptions) . ')', $rows);
        $this->log->table($table);

        foreach ($exceptions as $exception) {
            $this->log->err((string)$exception);
        }
    }

    /**
     * Encode data method
     *
     * @param   $data
     * @return  array|string
     */
    public function encode($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                unset($data[$key]);
                $data[$this->encode($key)] = $this->encode($value);
            }
        } elseif (is_string($data)) {
            if (strlen($data) > 250) {
                $data = substr($data, 0, 250) . '...';
            }
            $data = utf8_encode($data);
        } elseif ($data instanceof ArrayObject) {
            /** @var $data ArrayObject */
            $data = $this->encode($data->getArrayCopy());
        } elseif (is_object($data)) {
            $data = get_class($data);
        }
        return $data;
    }
}