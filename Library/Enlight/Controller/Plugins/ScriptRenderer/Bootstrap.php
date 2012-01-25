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
 * The Enlight_Controller_Plugins_ScriptRenderer_Bootstrap is a standard plugin to render javascript files over the
 * controller. Used by the extjs application module.
 *
 * @category   Enlight
 * @package    Enlight_Extensions
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
class Enlight_Controller_Plugins_ScriptRenderer_Bootstrap extends Enlight_Plugin_Bootstrap
{
    /**
     * @var string Used for the Zend_Filter_Inflector
     */
    protected $target = ':module/:controller/:file:suffix';

    /**
     * @var string Used when no file parameter given.
     */
    protected $defaultFile = null;

    /**
     * @var array Filter rules for the Zend_Filter_Inflector
     */
    protected $filterRules = array(
        ':module' => array('Word_CamelCaseToUnderscore', 'StringToLower'),
        ':controller' => array('Word_CamelCaseToUnderscore', 'StringToLower'),
        ':file' => array('Word_CamelCaseToUnderscore', 'StringToLower'),
        'suffix' => '.js'
    );

    /**
     * @var array Will be set in the response instance on pre dispatch.
     */
    protected $headers = array('Content-Type' => 'application/javascript;charset=utf-8');

    /**
     * @var bool Flag if the view already rendered
     */
    protected $render = false;

    /**
     * @var Enlight_Controller_Plugins_ViewRenderer_Bootstrap Instance of the enlight view renderer.
     */
    protected $viewRenderer;

    /**
     * Plugin install method.
     * Subscribes the Enlight_Controller_Action_PreDispatch event to
     * render the script template and set the headers in the response instance.
     */
    public function init()
    {
        $event = new Enlight_Event_Handler_Default(
            'Enlight_Controller_Action_PreDispatch',
            array($this, 'onPreDispatch'),
            300
        );
        $this->Application()->Events()->registerListener($event);
    }

    /**
     * Loads the script template, if no set.
     *
     * @param   Enlight_Event_EventArgs $args
     */
    public function onPreDispatch(Enlight_Event_EventArgs $args)
    {
        if (!$this->render) {
            return;
        }

        $this->render = false;

        if ($this->viewRenderer->Action()->View()->hasTemplate()
                || !$this->viewRenderer->shouldRender()) {
            return;
        }

        if (($template = $this->getTemplateName()) !== null) {
            $this->viewRenderer->Action()->View()->loadTemplate($template);
            foreach ($this->headers as $name => $value) {
                $this->viewRenderer->Action()->Response()->setHeader($name, $value);
            }
        }
    }

    /**
     * Sets the render flag. Loads the view renderer.
     *
     * @param   bool $flag
     * @return  Enlight_Controller_Plugins_ScriptRenderer_Bootstrap
     */
    public function setRender($flag = true)
    {
        $this->setViewRenderer();
        $this->render = $flag ? true : false;
        ;
        return $this;
    }

    /**
     * Returns the template name.
     *
     * @return  string
     */
    public function getTemplateName()
    {
        $request = $this->viewRenderer->Action()->Request();
        $dispatcher = $this->viewRenderer->Front()->Dispatcher();

        $moduleName = $dispatcher->formatModuleName($request->getModuleName());
        $controllerName = $dispatcher->formatControllerName($request->getControllerName());

        $inflector = new Zend_Filter_Inflector($this->target);
        $inflector->setRules($this->filterRules);
        $inflector->setThrowTargetExceptionsOn(false);

        $fileName = $request->getParam('file', $this->defaultFile);
        $fileName = ltrim(dirname($fileName) . '/' . basename($fileName, '.js'), '/.');

        if (empty($fileName)) {
            return null;
        }

        $templateName = $inflector->filter(array(
            'module' => $moduleName,
            'controller' => $controllerName,
            'file' => $fileName)
        );

        return $templateName;
    }

    /**
     * Sets the view renderer instance
     *
     * @param Enlight_Controller_Plugins_ViewRenderer_Bootstrap|null $viewRenderer
     * @return Enlight_Controller_Plugins_ScriptRenderer_Bootstrap
     */
    public function setViewRenderer(Enlight_Controller_Plugins_ViewRenderer_Bootstrap $viewRenderer = null)
    {
        if ($viewRenderer === null) {
            $viewRenderer = $this->Collection()->get('ViewRenderer');
        }
        $this->viewRenderer = $viewRenderer;
        return $this;
    }
}