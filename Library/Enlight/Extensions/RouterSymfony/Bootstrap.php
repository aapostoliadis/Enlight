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

use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\YamlFileLoader;

/**
 * Integrated the component of the router from Symfony in Enlight.
 * Supports the management of routes via yaml files and the plugin configuration.
 *
 * @category   Enlight
 * @package    Enlight_Extensions
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
class Enlight_Extensions_RouterSymfony_Bootstrap extends Enlight_Plugin_Bootstrap_Config
{
    /**
     * Installs the router plugin.
     * Registers the router events.
     */
    public function install()
    {
        $this->subscribeEvent(
            'Enlight_Bootstrap_InitResource_Routes',
            'onInitResourceRoutes'
        );

        $this->subscribeEvent(
            'Enlight_Controller_Router_Route',
            'onRoute',
            $this->Config()->get('routeIndex')
        );

        $this->subscribeEvent(
            'Enlight_Controller_Router_Assemble',
            'onAssemble',
            $this->Config()->get('assembleIndex')
        );
    }

    /**
     * Returns the route collection on event call
     *
     * @param Enlight_Event_EventArgs $args
     * @return \Symfony\Component\Routing\RouteCollection
     */
    public function onInitResourceRoutes(Enlight_Event_EventArgs $args)
    {
        $configRoutes = $this->Config()->get('routes');
        if(is_string($configRoutes)) {
            $locator = new FileLocator(array('.'));
            $loader = new YamlFileLoader($locator);
            return $loader->load($configRoutes);
        } elseif($configRoutes instanceof Enlight_Config) {
            /** @var $configRoutes Zend_Config */
            $configRoutes = $configRoutes->toArray();
            $routes = new RouteCollection();
            foreach($configRoutes as $routeKey => $route) {
                $routes->add(
                    isset($route->name) ? $route->name : $routeKey,
                    new Route(
                        $route['pattern'],
                        isset($route['defaults']) ? $route['defaults'] : array(),
                        isset($route['requirements']) ? $route['requirements'] : array(),
                        isset($route['options']) ? $route['options'] : array()
                    )
                );
            }
            return $routes;
        }
        return null;
    }

    /**
     * Returns the symfony request context.
     *
     * @param Enlight_Controller_Request_RequestHttp $request
     * @return Symfony\Component\Routing\RequestContext
     */
    protected function getRequestContext($request)
    {
        return new RequestContext(
            '',
            $request->getMethod(),
            $request->getHttpHost(),
            $request->getScheme()
        );
    }

    /**
     * Routes the path information, if the router symfony match it.
     *
     * @param Enlight_Event_EventArgs $args
     * @return array|null
     */
    public function onRoute(Enlight_Event_EventArgs $args)
    {
        /** @var $request Enlight_Controller_Request_RequestHttp */
        $request = $args->getRequest();
        /** @var $routes Symfony\Component\Routing\RouteCollection */
        $routes = $this->Application()->Routes();

        $context = $this->getRequestContext($request);
        $matcher = new UrlMatcher($routes, $context);

        try {
            return $matcher->match($request->getPathInfo());
        } catch(ResourceNotFoundException $e) {
            return null;
        }
    }

    /**
     * Assembles a url with the symfony url generator based on the parameters.
     *
     * @param   Enlight_Event_EventArgs $args
     * @return  string|null
     */
    public function onAssemble(Enlight_Event_EventArgs $args)
    {
        /** @var $request Request */
        $request = $args->get('subject')->Front()->Request();
        /** @var $params array */
        $params = $args->get('params');
        /** @var $routes Symfony\Component\Routing\RouteCollection */
        $routes = $this->Application()->Routes();

        $context = $this->getRequestContext($request);
        $matcher = new UrlGenerator($routes, $context);

        foreach($routes as $name => $route) {
            try {
                if(($url = $matcher->generate($name, $params)) !== null) {
                    return ltrim($url, '/');
                }
            } catch(Exception $e) { }
        }
        return null;
    }
}