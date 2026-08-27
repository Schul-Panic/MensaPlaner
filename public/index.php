<?php

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;

$app = AppFactory::create();

$app->get('/', function ($request, $response) {
    $response->getBody()->write('Willkommen beim MensaPlaner');
    return $response;
});

$app->get('/users', function ($request, $response) {

    require_once __DIR__ . '/../app/Controller/UserController.php';

    $controller = new UserController();

    $users = $controller->index();

    $response->getBody()->write(
        json_encode($users)
    );

    return $response
        ->withHeader('Content-Type', 'application/json');
});

$app->run();