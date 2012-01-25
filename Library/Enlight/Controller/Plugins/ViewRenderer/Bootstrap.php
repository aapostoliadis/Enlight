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
 * The Enlight_Controller_Plugins_ViewRenderer_Bootstrap is a standard plugin to load the specify
 * controller action template and render it.  The template will load in the
 * pre dispatch and rendered in the post dispatch.
 *
 * @category   Enlight
 * @package    Enlight_Extensions
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
class Enlight_Controller_Plugins_ViewRenderer_Bootstrap extends Enlight_Plugin_Bootstrap_Default
{
    /**
     * @var bool Flag if the view should never render
     */
    protected $neverRender = false;

    /**
     * @var bool Flag if the view shouldn't render
     */
    protected $noRender = false;

    /**
     * @var Enlight_Controller_Front Instance of the enlight front controller.
     * Will be set in the Enlight_Controller_Front_DispatchLoopStartup event.
     * Used to get the module, controller and action name in the getTempalteName function.
     */
    protected $front;

    /**
     * @var Enlight_Controller_Action Used to load and set the template.
     * Will be set in the onActionInit function.
     */
    protected $action;

    /**
     * @var Enlight_Template_Manager Is used when the view is initialized
     */
    protected $engine;

    /**
     * Initialisation of the view renderer.
     * Subscribes the Enlight_Controller_Front_DispatchLoopStartup event, to
     * draw the front controller and the template manager out of the Enlight_Event_EventArgs
     * and set them into the internal variables.
     * In the Enlight_Controller_Action_PostDispatch event, enlight will render the template and
     * reset the template in the view instance of the Enlight_Controller_Action.
     * In the Enlight_Controller_Action_PreDispatch event, enlight will load the template object.
     * In the Enlight_Controller_Action_Init event, enlight will drawn the Enlight_Controller_Action out of
     * the Enlight_Event_EventArgs and initial the Enlight_Template_Manager and the Enlight_View_Default.
     *
     * @return void
     */
    public function init()
    {
        if(!$this->Collection()) {
            return;
        }
        $event = new Enlight_Event_Handler_Default(
            'Enlight_Controller_Front_DispatchLoopStartup',
            array($this, 'onDispatchLoopStartup'),
            400
        );
        $this->Application()->Events()->registerListener($event);
        $event = new Enlight_Event_Handler_Default(
            'Enlight_Controller_Action_PostDispatch',
            array($this, 'onPostDispatch'),
            400
        );
        $this->Application()->Events()->registerListener($event);
        $event = new Enlight_Event_Handler_Default(
            'Enlight_Controller_Action_PreDispatch',
            array($this, 'onPreDispatch'),
            400
        );
        $this->Application()->Events()->registerListener($event);
        $event = new Enlight_Event_Handler_Default(
            'Enlight_Controller_Action_Init',
            array($this, 'onActionInit'),
            400
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
            $this->Action()->View()->setTemplate();
        }
        $this->setNoRender(false);
    }

    /**
     * @param   Enlight_Event_EventArgs $args
     */
    public function onPreDispatch(Enlight_Event_EventArgs $args)
    {
        if ($this->shouldRender() && !$this->Action()->View()->hasTemplate()) {
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
        if ($this->engine === null) {
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
     * @param   string      $template
     * @param   string|null $name
     * @return  Enlight_Controller_Plugins_ViewRenderer_Bootstrap
     */
    public function renderTemplate($template, $name = null)
    {
        $action = $this->Action();

        $this->Application()->Events()->notify(
            'Enlight_Plugins_ViewRenderer_PreRender',
            array('subject' => $this, 'template' => $template)
        );

        $render = $action->View()->render($template);
        $render = $this->Application()->Events()->filter(
            'Enlight_Plugins_ViewRenderer_FilterRender',
            $render,
            array('subject' => $this, 'template' => $template)
        );

        $action->Response()->appendBody($render, $name);

        $this->Application()->Events()->notify(
            'Enlight_Plugins_ViewRenderer_PostRender',
            array('subject' => $this, 'template' => $template)
        );

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
    public function shouldRender()
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
        $this->noRender = $flag ? true : false;
        ;
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