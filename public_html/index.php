<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/settings.php';
require __API__ . '/functions.php';

session_start([
    'cookie_lifetime' => 86400,
]);

// Instantiate the app
$settings = require __API__ . 'slim/settings.php';
$app = new \Slim\App($settings);

// Set up the error handler
require __API__ . 'slim/error_handler.php';

// Set up dependencies
require __API__ . 'slim/dependencies.php';

// Register routes
require __API__ . 'slim/routes.php';

// Register middleware
require __API__ . 'slim/middleware.php';

$container = $app->getContainer();
$container['errorHandler'] = function ($c) {
    return function ($request, $response, $exception) use ($c) {
        $c['response']->withStatus(200);
        return $c['response']->withJson(
                array(
                    'error' => "An API error occurred.\n" . $exception->getMessage(),
                    'data' => array(),
                    'query' => ''
                ));
    };
};
// Register component on container
$container['view'] = function ($container) {
    return new \Slim\Views\PhpRenderer(__PAGES__);
};

// Run app
$app->run();