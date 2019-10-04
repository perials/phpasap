<?php
use core\classes\App;

/* We now register our autoloader and include all the required files */
require 'bootstrap.php';

// Overwrite default properties with Swoole specific ones
App::register('request', function(&$app) {
    return new core\classes\Swoole_Request($app);
});
App::register('route', function(&$app) {
    return new core\classes\Swoole_Route_Handler($app);
});
App::register('response', function(&$app) {
    return new core\classes\Swoole_Response_Handler($app);
});
App::register('session', function(&$app) {
    return new core\classes\Swoole_Session_Handler($app);
});

// Middleware for static files
$static = [
    'css'  => 'text/css',
    'js'   => 'text/javascript',
    'png'  => 'image/png',
    'gif'  => 'image/gif',
    'jpg'  => 'image/jpg',
    'jpeg' => 'image/jpg',
    'mp4'  => 'video/mp4'
];
App::use(function($app, $next) use ($static) {
    $static_file = ROOT . $app->swoole_request->server['request_uri'];
    if (! file_exists($static_file)) {
        return $next();
    }
    $type = pathinfo($static_file, PATHINFO_EXTENSION);
    if (! isset($static[$type])) {
        return $next();
    }
    $app->swoole_response->header('Content-Type', $static[$type]);
    $app->swoole_response->sendfile($static_file);
});

// Route middlewares
App::get('/', 'Welcome_Controller@index');
App::controller('crud', 'Crud_Controller');
App::controller('todo', 'Todo_Controller');

$http = new swoole_http_server('0.0.0.0', 3000);
$http->on('request', function($request, $response) {
    // Create new app instance
    $app = new App();

    // Set swoole request and response
    $app->swoole_request = $request;
    $app->swoole_response = $response;

    // Map the current request with routing array and capture if any match occurs
    $app->map();

    // If match occurs the appropriate controller method will be called with passed arguments
    $app->dispatch();

    $app->clear();
});
$http->start();
