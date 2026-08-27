<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Controller\UserController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

session_start();

$app = AppFactory::create();
$app->addErrorMiddleware(true, true, true);
$app->addBodyParsingMiddleware();

$app->get('/', function (Request $request, Response $response) {
    ob_start();
    require __DIR__ . '/../app/View/home.php';
    $html = ob_get_clean();

    $response->getBody()->write($html);

    return $response;
});

$app->post('/', function (Request $request, Response $response) {
    return $response->withHeader('Location', '/')->withStatus(302);
});

$app->get('/users', [UserController::class, 'showUsers']);
$app->post('/users', [UserController::class, 'addUser']);
$app->post('/users/{id}/delete', [UserController::class, 'deleteUser']);

$app->run();
