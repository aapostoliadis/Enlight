<?php
function smarty_function_action($params, Enlight_Template_Default $template)
{
    /** @var $front Enlight_Controller_Front */
    $front = Enlight_Application::Instance()->Front();
    $dispatcher = clone $front->Dispatcher();

    $modules = $dispatcher->getControllerDirectory();
    if (empty($modules)) {
        $e = new Exception('Action helper depends on valid front controller instance');
        //$e->setView($view);
        throw $e;
    }

    $request  = $front->Request();
    $response = $front->Response();

    if (empty($request) || empty($response)) {
        $e = new Exception('Action view helper requires both a registered request and response object in the front controller instance');
        //$e->setView($view);
        throw $e;
    }

    $request  = clone $request;
    $response = clone $response;

    //$request->clearParams();
    $response->clearHeaders()
             ->clearRawHeaders()
             ->clearBody();

    $params = array_merge($params, $request->getParams());

    $request->setModuleName(null)
            ->setControllerName(null)
            ->setActionName(null);

    $request->setParams($params)
            ->setDispatched(true);

    $dispatcher->dispatch($request, $response);

    if (!$request->isDispatched() || $response->isRedirect()) {
        // forwards and redirects render nothing
        return '';
    }

    $return = $response->getBody();

    return $return;
}