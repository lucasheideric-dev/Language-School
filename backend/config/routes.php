<?php

use Cake\Http\Middleware\CsrfProtectionMiddleware;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Route\DashedRoute;
use Cake\Routing\Router;

return function (RouteBuilder $routes) {
    $routes->setRouteClass(DashedRoute::class);

    $routes->prefix('api', function (RouteBuilder $routes) {
        $routes->setExtensions(['json']);
        $routes->connect('/login', ['controller' => 'Auth', 'action' => 'login']);
        $routes->fallbacks(DashedRoute::class);
    });

    $routes->scope('/', function (RouteBuilder $routes) {
        // $routes->registerMiddleware('csrf', new CsrfProtectionMiddleware(['httpOnly' => true]));
        // $routes->applyMiddleware('csrf');

        $routes->connect('/', ['controller' => 'Pages', 'action' => 'display', 'home']);
        $routes->connect('/pages/*', ['controller' => 'Pages', 'action' => 'display']);
        $routes->fallbacks(DashedRoute::class);
    });
};
