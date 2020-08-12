<?php


use Slim\App;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;

require '../vendor/autoload.php';

if (session_id() === '') {
    session_start();
}

// Create Slim app
$app = new App([
    'settings' => [
        'displayErrorDetails' => true
    ]
]);

// Fetch DI Container
$container = $app->getContainer();

// Register Twig View helper
$container['view'] = function($c) {
    $view = new Twig('./../view'/*, [
        'cache' => './../view/.cache'
    ]*/);

    // Instantiate and add Slim specific extension
    $basePath = rtrim(str_ireplace('index.php', '', $c['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new TwigExtension($c['router'], $basePath));
    return $view;
};

// Adds all route files
foreach (glob('./../route/*.php') as $file) {
    require $file;
}

// Run app
$app->run();

