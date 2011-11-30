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
class Enlight_Controller_Plugins_ViewRenderer_Bootstrap extends Enlight_Plugin_Bootstrap_Default
{
    /**
     * @var bool
     */
    protected $neverRender = false;

    /**
     * @var bool
     */
    protected $noRender = false;

    /**
     * @var Enlight_Controller_Front
     */
    protected $front;

    /**
     * @var Enlight_Controller_Action
     */
    protected $action;

    /**
     * @var Enlight_Template_Manager
     */
    protected $engine;

    /**
     * @return void
     */
    public function init()
    {
        $event = new Enlight_Event_Handler_Default(
            'Enlight_Controller_Front_DispatchLoopStartup',
            400,
            array($this, 'onDispatchLoopStartup')
        );
        $this->Application()->Events()->registerListener($event);
        $event = new Enlight_Event_Handler_Default(
            'Enlight_Controller_Action_PostDispatch',
            400,
            array($this, 'onPostDispatch')
        );
        $this->Application()->Events()->registerListener($event);
        $event = new Enlight_Event_Handler_Default(
            'Enlight_Controller_Action_PreDispatch',
            400,
            array($this, 'onPreDispatch')
        );
        $this->Application()->Events()->registerListener($event);
        $event = new Enlight_Event_Handler_Default(
            'Enlight_Controller_Action_Init',
            400,
            array($this, 'onActionInit')
        );
        $this->Application()->Events()->registerListener($event);
    }

    /**
     * @param   Enlight_Event_EventArgs $args
     * @return 
     */
    public function onDispatchLoopStartup(Enlight_Event_EventArgs $args)
    {
        if ($args->getSubject()->getParam('noViewRenderer')) {
            return;
        }
        $this->front = $args->getSubject();
        $this->engine = $this->Application()->Bootstrap()->getResource('Template');
    }

    /**
     * @param   Enlight_Event_EventArgs $args
     */
    public function onPostDispatch(Enlight_Event_EventArgs $args)
    {
        if ($this->shouldRender() && $this->Action()->View()->hasTemplate()) {
            $this->render();
        }
    }

    /**
     * @param   Enlight_Event_EventArgs $args
     */
    public function onPreDispatch(Enlight_Event_EventArgs $args)
    {
        if($this->shouldRender() && !$this->Action()->View()->hasTemplate()) {
            $this->Action()->View()->loadTemplate($this->getTemplateName());
        }
    }

    /**
     * @param   Enlight_Event_EventArgs $args
     */
    public function onActionInit(Enlight_Event_EventArgs $args)
    {
        $this->action = $args->getSubject();
        $this->initEngine();
        $this->initView();
    }

    /**
     * @return  void
     */
    protected function initEngine()
    {
        if($this->engine === null) {
            $this->engine = $this->Application()->Bootstrap()->getResource('Template');
        }
    }

    /**
     * @return  void
     */
    protected function initView()
    {
        $view = new Enlight_View_Default($this->engine);
        $this->Action()->setView($view);
    }

    /**
     * @param   string $template
     * @param   string|null $name
     * @return  Enlight_Controller_Plugins_ViewRenderer_Bootstrap
     */
    public function renderTemplate($template, $name = null)
    {
        $action = $this->Action();

        $this->Application()->Events()->notify('Enlight_Plugins_ViewRenderer_PreRender', array('subject'=>$this, 'template'=>$template));

        $render = $action->View()->render($template);
        $render = $this->Application()->Events()->filter('Enlight_Plugins_ViewRenderer_FilterRender', $render, array('subject'=>$this, 'template'=>$template));

        $action->Response()->appendBody(
            $render,
            $name
        );
        //$this->setNoRender();

        $this->Application()->Events()->notify('Enlight_Plugins_ViewRenderer_PostRender', array('subject'=>$this));

        return $this;
    }

    /**
     * @return  Enlight_Controller_Plugins_ViewRenderer_Bootstrap
     */
    public function render()
    {
        $template = $this->Action()->View()->Template();
        return $this->renderTemplate($template);
    }

    /**
     * @return  bool
     */
    protected function shouldRender()
    {
        return (!$this->Front()->getParam('noViewRenderer')
            && !$this->neverRender
            && !$this->noRender
            && $this->Action()
            && $this->Action()->Request()->isDispatched()
            && !$this->Action()->Response()->isRedirect()
        );
    }

    /**
     * @return  Enlight_Controller_Front
     */
    public function Front()
    {
        return $this->front;
    }

    /**
     * @return  Enlight_Controller_Action
     */
    public function Action()
    {
        return $this->action;
    }

    /**
     * @param   bool $flag
     * @return  Enlight_Controller_Plugins_ViewRenderer_Bootstrap
     */
    public function setNoRender($flag = true)
    {
        $this->noRender = $flag ? true : false;;
        return $this;
    }

    /**
     * @param   bool $flag
     * @return  Enlight_Controller_Plugins_ViewRenderer_Bootstrap
     */
    public function setNeverRender($flag = true)
    {
        $this->neverRender = $flag ? true : false;
        return $this;
    }

    /**
     * @return  string
     */
    public function getTemplateName()
    {
        $request = $this->Action()->Request();
        $moduleName = $this->Front()->Dispatcher()->formatModuleName($request->getModuleName());
        $controllerName = $this->Front()->Dispatcher()->formatControllerName($request->getControllerName());
        $actionName = $this->Front()->Dispatcher()->formatActionName($request->getActionName());

        $parts = array($moduleName, $controllerName, $actionName);
        foreach ($parts as &$part) {
            $part = preg_replace('#[A-Z]#', '_$0', $part);
            $part = trim($part, '_');
            $part = strtolower($part);
        }

        $templateName = implode(DIRECTORY_SEPARATOR, $parts) . '.tpl';
        return $templateName;
    }
}