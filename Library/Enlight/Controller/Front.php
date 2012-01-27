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
 * @package    Enlight_Controller
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 * @version    $Id$
 * @author     Heiner Lohaus
 * @author     $Author$
 */

/**
 * Enlight_Controller_Front managed everything between the request, response, dispatcher and router.
 *
 * The Enlight_Controller_Front represents the core controller. It manages everything (classes, data, sequence)
 * between the request, response, dispatcher and router. If these are not set (classes, data, sequence), the
 * controller loads them automatically. If nothing else is specified in the configuration, the controller
 * loads the default plugins viewRenderer and errorHandler. The controller running the dispatch of the request
 * unless according to request everything was dispatched. it catches exceptions automatically and sets them into the
 * response object. Finally it sends the response if nothing else is specified in the configuration.
 *
 * @category   Enlight
 * @package    Enlight_Controller
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
class Enlight_Controller_Front extends Enlight_Class implements Enlight_Hook, Enlight_Singleton
{
    /**
     * @var Enlight_Plugin_Namespace_Loader contains an instance of the Enlight_Plugin_Namespace_Loader
     */
    protected $plugins;

    /**
     * @var Enlight_Controller_Router contains an instance of the Enlight_Controller_Router.
     * Used to route the request to the controller/action.
     */
    protected $router;

    /**
     * @var Enlight_Controller_Dispatcher contains in instance of the
     * Enlight_Controller_Dispatcher. Used to dispatch the request.
     */
    protected $dispatcher;

    /**
     * @var Enlight_Controller_Request_RequestHttp contains an instance of the
     * Enlight_Controller_Request_RequestHttp. Used for the routing,
     * the different events which will be notified in the dispatch function and for the
     * dispatch itself.
     */
    protected $request;

    /**
     * @var Enlight_Controller_Response_ResponseHttp contains an
     * instance of the Enlight_Controller_Response_ResponseHttp. Used for the dispatch of the request
     * and to log the thrown exception. After the dispatch, the response will be sent.
     */
    protected $response;

    /**
     * @var bool Flag whether an exception should be thrown directly at the dispatch. If the
     * flag is set to false, the exceptions is set in the response instance.
     */
    protected $throwExceptions;

    /**
     * @var bool Flag whether the response object should be returned in the dispatch.
     */
    protected $returnResponse;

    /**
     * @var array Contains all invoked params. The invoked params can be set by the setParam/s function and
     * can be accessed by the getParams function.
     */
    protected $invokeParams = array();

    /**
     * Dispatch function of the front controller.
     *
     * If the flags noErrorHandler and noViewRenderer aren't set, the error handler and the view renderer
     * plugins will be loaded. After the plugins loaded the Enlight_Controller_Front_StartDispatch
     * event is notified.
     * After the event is done, enlight sets the router, dispatcher, request and response object automatically.
     * If the objects has been set, the Enlight_Controller_Front_RouteStartup event is notified.
     * After the event is done, the route routes the request to controller/action.
     * Then the Enlight_Controller_Front_RouteShutdown event and the Enlight_Controller_Front_DispatchLoopStartup
     * event are notified. After this events the controller runs the dispatch
     * of the request unless according to request everything was dispatched. During the dispatch
     * two events are notified:<br>
     *  - Enlight_Controller_Front_PreDispatch  => before the dispatch<br>
     *  - Enlight_Controller_Front_PostDispatch => after the dispatch<br><br>
     * When everything is dispatched the Enlight_Controller_Front_DispatchLoopShutdown event will be notified.
     * At last the response is sent. As well as the dispatch, two events are notified:
     *  - Enlight_Controller_Front_SendResponse      => before the response is sent<br>
     *  - Enlight_Controller_Front_AfterSendResponse => after the response is sent
     *
     * @throws  Exception
     * @return  Enlight_Controller_Response_ResponseHttp
     */
    public function dispatch()
    {
        if (!$this->getParam('noErrorHandler')) {
            $this->Plugins()->load('ErrorHandler');
        }
        if (!$this->getParam('noViewRenderer')) {
            $this->Plugins()->load('ViewRenderer');
        }

        Enlight_Application::Instance()->Events()->notify(
            'Enlight_Controller_Front_StartDispatch',
            array('subject' => $this)
        );

        if (!$this->router) {
            $this->setRouter('Enlight_Controller_Router_Default');
        }
        if (!$this->dispatcher) {
            $this->setDispatcher('Enlight_Controller_Dispatcher_Default');
        }
        if (!$this->request) {
            $this->setRequest('Enlight_Controller_Request_RequestHttp');
        }
        if (!$this->response) {
            $this->setResponse('Enlight_Controller_Response_ResponseHttp');
        }

        try {
            /**
             * Notify plugins of router startup
             */
            Enlight_Application::Instance()->Events()->notify(
                'Enlight_Controller_Front_RouteStartup',
                array('subject' => $this)
            );

            /**
             * Route request to controller/action, if a router is provided
             */
            try {
                $this->router->route($this->request);
            }
            catch (Exception $e) {
                if ($this->throwExceptions()) {
                    throw $e;
                }
                $this->response->setException($e);
            }

            /**
             * Notify plugins of router completion
             */
            Enlight_Application::Instance()->Events()->notify(
                'Enlight_Controller_Front_RouteShutdown',
                array('subject' => $this, 'request' => $this->Request())
            );

            /**
             * Notify plugins of dispatch loop startup
             */
            Enlight_Application::Instance()->Events()->notify(
                'Enlight_Controller_Front_DispatchLoopStartup',
                array('subject' => $this, 'request' => $this->Request())
            );

            /**
             *  Attempts to dispatch the controller/action. If the $this->request
             *  indicates that it needs to be dispatched, it moves to the next
             *  action in the request.
             */
            do {
                $this->request->setDispatched(true);

                /**
                 * Notify plugins of dispatch startup
                 */
                try {
                    Enlight_Application::Instance()->Events()->notify(
                        'Enlight_Controller_Front_PreDispatch',
                        array('subject' => $this, 'request' => $this->Request())
                    );

                    /**
                     * Skip requested action if preDispatch() has reset it
                     */
                    if (!$this->request->isDispatched()) {
                        continue;
                    }

                    /**
                     * Dispatch request
                     */
                    try {
                        $this->dispatcher->dispatch($this->request, $this->response);
                    }
                    catch (Exception $e) {
                        if ($this->throwExceptions()) {
                            throw $e;
                        }
                        $this->response->setException($e);
                    }
                }
                catch (Exception $e) {
                    if ($this->throwExceptions()) {
                        throw $e;
                    }
                    $this->response->setException($e);
                }
                /**
                 * Notify plugins of dispatch completion
                 */
                Enlight_Application::Instance()->Events()->notify(
                    'Enlight_Controller_Front_PostDispatch',
                    array('subject' => $this, 'request' => $this->Request())
                );
            } while (!$this->request->isDispatched());
        }
        catch (Exception $e) {
            if ($this->throwExceptions()) {
                throw $e;
            }
            $this->response->setException($e);
        }

        /**
         * Notify plugins of dispatch loop completion
         */
        try {
            Enlight_Application::Instance()->Events()->notify(
                'Enlight_Controller_Front_DispatchLoopShutdown',
                array('subject' => $this)
            );
        }
        catch (Exception $e) {
            if ($this->throwExceptions()) {
                throw $e;
            }
            $this->response->setException($e);
        }

        if ($this->returnResponse()) {
            return $this->response;
        }

        if (!Enlight_Application::Instance()->Events()->notifyUntil(
            'Enlight_Controller_Front_SendResponse',
            array('subject' => $this, 'response' => $this->Response(), 'request' => $this->Request())
        )
        ) {
            $this->Response()->sendResponse();
        }

        Enlight_Application::Instance()->Events()->notify(
            'Enlight_Controller_Front_AfterSendResponse',
            array('subject' => $this, 'request' => $this->Request())
        );

        return 0;
    }

    /**
     * Setter method for the plugin property.
     *
     * @throws  Enlight_Exception
     * @param   string|Enlight_Plugin_Namespace $plugins
     * @return  Enlight_Controller_Front
     */
    public function setPlugins(Enlight_Plugin_Namespace $plugins = null)
    {
        if ($plugins === null) {
            $plugins = new Enlight_Plugin_Namespace_Loader('Controller');
            $plugins->addPrefixPath('Enlight_Controller_Plugins', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Plugins');
        }
        $this->plugins = $plugins;
        return $this;
    }

    /**
     * Setter method for the router. Sets the front controller instance
     * automatically in the given router.
     *
     * @throws  Enlight_Exception
     * @param   string|Enlight_Controller_Router $router
     * @return  Enlight_Controller_Front
     */
    public function setRouter($router)
    {
        if (is_string($router)) {
            $router = new $router();
        }
        if (!$router instanceof Enlight_Controller_Router) {
            throw new Enlight_Exception('Invalid router class');
        }
        $router->setFront($this);
        $this->router = $router;
        return $this;
    }

    /**
     * Setter method for the dispatcher. Sets the front controller instance
     * automatically in the given dispatcher.
     *
     * @throws  Enlight_Exception
     * @param   string|Enlight_Controller_Dispatcher $dispatcher
     * @return  Enlight_Controller_Front
     */
    public function setDispatcher($dispatcher)
    {
        if (is_string($dispatcher)) {
            $dispatcher = new $dispatcher();
        }
        if (!$dispatcher instanceof Enlight_Controller_Dispatcher) {
            throw new Enlight_Exception('Invalid dispatcher class');
        }
        $dispatcher->setFront($this);
        $this->dispatcher = $dispatcher;
        return $this;
    }

    /**
     * Sets the request instance
     *
     * @throws  Enlight_Exception
     * @param   $request
     * @return  Enlight_Controller_Front
     */
    public function setRequest($request)
    {
        if (is_string($request)) {
            $request = new $request();
        }
        if (!$request instanceof Enlight_Controller_Request_Request) {
            throw new Enlight_Exception('Invalid request class');
        }
        $this->request = $request;
        return $this;
    }

    /**
     * Sets the response instance
     *
     * @throws  Enlight_Exception
     * @param   $response
     * @return  Enlight_Controller_Front
     */
    public function setResponse($response)
    {
        if (is_string($response)) {
            $response = new $response();
        }
        if (!$response instanceof Enlight_Controller_Response_Response) {
            throw new Enlight_Exception('Invalid response class');
        }
        $this->response = $response;
        return $this;
    }

    /**
     * Sets the return response flag
     * Returns the value of the return response flag
     *
     * @param   null $flag
     * @return  bool|Enlight_Controller_Front
     */
    public function returnResponse($flag = null)
    {
        if (true === $flag) {
            $this->returnResponse = true;
            return $this;
        } elseif (false === $flag) {
            $this->returnResponse = false;
            return $this;
        }
        return $this->returnResponse;
    }

    /**
     * Getter method for the plugin property.
     *
     * @return Enlight_Plugin_Namespace_Loader
     */
    public function Plugins()
    {
        if ($this->plugins === null) {
            $this->setPlugins();
        }
        return $this->plugins;
    }

    /**
     * Returns the router instance.
     *
     * @return  Enlight_Controller_Router
     */
    public function Router()
    {
        return $this->router;
    }

    /**
     * Returns the request instance.
     *
     * @return  Enlight_Controller_Request_RequestHttp
     */
    public function Request()
    {
        return $this->request;
    }

    /**
     * Returns the response instance.
     *
     * @return  Enlight_Controller_Response_ResponseHttp
     */
    public function Response()
    {
        if ($this->response === null) {
            $this->setResponse('Enlight_Controller_Response_ResponseHttp');
        }
        return $this->response;
    }

    /**
     * Returns the dispatcher instance.
     *
     * @return  Enlight_Controller_Dispatcher_Default
     */
    public function Dispatcher()
    {
        if ($this->dispatcher === null) {
            $this->setDispatcher('Enlight_Controller_Dispatcher_Default');
        }
        return $this->dispatcher;
    }

    /**
     * Setter method for the throwException property.
     *
     * @param   bool|null $flag
     * @return  bool|Enlight_Controller_Front
     */
    public function throwExceptions($flag = null)
    {
        if ($flag !== null) {
            $this->throwExceptions = (bool)$flag;
            return $this;
        }
        return $this->throwExceptions;
    }

    /**
     * Setter method to set a single parameter into the invokeParams property.
     *
     * @param   string $name
     * @param   mixed  $value
     * @return  Enlight_Controller_Front
     */
    public function setParam($name, $value)
    {
        $name = (string)$name;
        $this->invokeParams[$name] = $value;
        return $this;
    }

    /**
     * Setter method for the invokeParams property.
     *
     * @param   array $params
     * @return  Enlight_Controller_Front
     */
    public function setParams(array $params)
    {
        $this->invokeParams = array_merge($this->invokeParams, $params);
        return $this;
    }

    /**
     * Sets a invoke param by name.
     *
     * @param   $name
     * @return  mixed
     */
    public function getParam($name)
    {
        if (isset($this->invokeParams[$name])) {
            return $this->invokeParams[$name];
        }
        return null;
    }

    /**
     * Returns the list of invoked params.
     *
     * @return  array
     */
    public function getParams()
    {
        return $this->invokeParams;
    }
}